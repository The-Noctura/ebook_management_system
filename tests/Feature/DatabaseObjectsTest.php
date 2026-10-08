<?php

use App\Models\Ebook;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseObjectsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped('Stored procedures, triggers, and functions require MySQL.');
        }
    }

    public function test_read_procedure_updates_status_and_timestamp_and_trigger_records_history(): void
    {
        $user = User::factory()->create();
        $ebook = $this->createEbook($user, false, 'read');
        $previousUpdatedAt = now()->subMinutes(5)->startOfSecond();

        DB::table('ebooks')
            ->where('id', $ebook->id)
            ->update(['updated_at' => $previousUpdatedAt]);

        DB::statement('CALL sp_mark_ebook_read(?, ?)', [$user->id, $ebook->id]);

        $ebook->refresh();
        $history = DB::table('ebook_status_history')->where('ebook_id', $ebook->id)->get();

        $this->assertTrue($ebook->is_read);
        $this->assertTrue($ebook->updated_at->greaterThan($previousUpdatedAt));
        $this->assertCount(1, $history);
        $this->assertSame(0, (int) $history->first()->old_status);
        $this->assertSame(1, (int) $history->first()->new_status);
        $this->assertNotNull($history->first()->changed_at);
    }

    public function test_unread_procedure_changes_status_and_trigger_records_reverse_transition(): void
    {
        $user = User::factory()->create();
        $ebook = $this->createEbook($user, true, 'unread');

        DB::statement('CALL sp_mark_ebook_unread(?, ?)', [$user->id, $ebook->id]);

        $ebook->refresh();
        $history = DB::table('ebook_status_history')->where('ebook_id', $ebook->id)->first();

        $this->assertFalse($ebook->is_read);
        $this->assertNotNull($history);
        $this->assertSame(1, (int) $history->old_status);
        $this->assertSame(0, (int) $history->new_status);
    }

    public function test_both_procedures_reject_ebooks_that_belong_to_another_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $ebook = $this->createEbook($owner, false, 'wrong-owner');

        foreach (['sp_mark_ebook_read', 'sp_mark_ebook_unread'] as $procedure) {
            try {
                DB::statement("CALL {$procedure}(?, ?)", [$otherUser->id, $ebook->id]);
                $this->fail("{$procedure} should reject an ebook owned by another user.");
            } catch (QueryException) {
                $this->assertFalse($ebook->fresh()->is_read);
            }
        }

        $this->assertSame(0, DB::table('ebook_status_history')->where('ebook_id', $ebook->id)->count());
    }

    public function test_status_trigger_records_only_actual_is_read_changes(): void
    {
        $user = User::factory()->create();
        $ebook = $this->createEbook($user, false, 'trigger');

        DB::table('ebooks')->where('id', $ebook->id)->update(['title' => 'Updated title']);

        $this->assertSame(0, DB::table('ebook_status_history')->where('ebook_id', $ebook->id)->count());

        DB::table('ebooks')
            ->where('id', $ebook->id)
            ->update(['is_read' => true]);

        $history = DB::table('ebook_status_history')->where('ebook_id', $ebook->id)->first();

        $this->assertNotNull($history);
        $this->assertSame(0, (int) $history->old_status);
        $this->assertSame(1, (int) $history->new_status);
    }

    public function test_read_count_function_counts_only_read_ebooks_for_the_requested_user(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $this->createEbook($user, true, 'read-one');
        $this->createEbook($user, true, 'read-two');
        $this->createEbook($user, false, 'unread');
        $this->createEbook($anotherUser, true, 'other-user-read');

        $userReadCount = DB::selectOne('SELECT fn_count_read_ebooks(?) AS read_count', [$user->id]);
        $anotherUserReadCount = DB::selectOne('SELECT fn_count_read_ebooks(?) AS read_count', [$anotherUser->id]);

        $this->assertSame(2, (int) $userReadCount->read_count);
        $this->assertSame(1, (int) $anotherUserReadCount->read_count);
    }

    private function createEbook(User $user, bool $isRead, string $suffix): Ebook
    {
        return Ebook::create([
            'user_id' => $user->id,
            'title' => "Database object test {$suffix}",
            'file_path' => "ebooks/user-{$user->id}/{$suffix}.pdf",
            'file_hash' => hash('sha256', "{$user->id}-{$suffix}"),
            'is_read' => $isRead,
        ]);
    }
}
