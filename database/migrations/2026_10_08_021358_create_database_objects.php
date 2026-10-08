<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trg_ebooks_after_update_is_read');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_count_read_ebooks');

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_mark_ebook_read');
        DB::unprepared(<<<'SQL'
      CREATE PROCEDURE sp_mark_ebook_read(
        IN p_user_id BIGINT UNSIGNED,
        IN p_ebook_id BIGINT UNSIGNED
      )
      BEGIN
        IF NOT EXISTS (
          SELECT 1
          FROM ebooks
          WHERE id = p_ebook_id
            AND user_id = p_user_id
        ) THEN
          SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Ebook not found or does not belong to user';
        END IF;

        UPDATE ebooks
        SET is_read = TRUE,
            updated_at = NOW()
        WHERE id = p_ebook_id
          AND user_id = p_user_id;
      END
      SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_mark_ebook_unread');
        DB::unprepared(<<<'SQL'
      CREATE PROCEDURE sp_mark_ebook_unread(
        IN p_user_id BIGINT UNSIGNED,
        IN p_ebook_id BIGINT UNSIGNED
      )
      BEGIN
        IF NOT EXISTS (
          SELECT 1
          FROM ebooks
          WHERE id = p_ebook_id
            AND user_id = p_user_id
        ) THEN
          SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Ebook not found or does not belong to user';
        END IF;

        UPDATE ebooks
        SET is_read = FALSE,
            updated_at = NOW()
        WHERE id = p_ebook_id
          AND user_id = p_user_id;
      END
      SQL);

        DB::unprepared(<<<'SQL'
      CREATE FUNCTION fn_count_read_ebooks(p_user_id BIGINT UNSIGNED)
      RETURNS BIGINT UNSIGNED
      NOT DETERMINISTIC
      READS SQL DATA
      BEGIN
        DECLARE read_ebook_count BIGINT UNSIGNED DEFAULT 0;

        SELECT COUNT(*)
        INTO read_ebook_count
        FROM ebooks
        WHERE user_id = p_user_id
          AND is_read = TRUE;

        RETURN read_ebook_count;
      END
      SQL);

        DB::unprepared(<<<'SQL'
      CREATE TRIGGER trg_ebooks_after_update_is_read
      AFTER UPDATE ON ebooks
      FOR EACH ROW
      BEGIN
        IF OLD.is_read <> NEW.is_read THEN
          INSERT INTO ebook_status_history (
            ebook_id,
            old_status,
            new_status,
            changed_at
          )
          VALUES (
            NEW.id,
            OLD.is_read,
            NEW.is_read,
            NOW()
          );
        END IF;
      END
      SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trg_ebooks_after_update_is_read');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_count_read_ebooks');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_mark_ebook_unread');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_mark_ebook_read');
    }
};
