<x-layout bare title="Koleksi Ebook Saya - {{ config('app.name', 'Laravel') }}">
    @php
        // Route upload dan buka PDF dibuat di C11. Sebelum itu, tombol tampil nonaktif.
        $uploadReady = Route::has('ebooks.create');
        $fileReady = Route::has('ebooks.file');
        $uploadUrl = $uploadReady ? route('ebooks.create') : '#';

        $total = $ebooks->count();
        $summary = $loadFailed ? null : ($total === 0 ? 'Belum ada ebook' : "{$readCount} dari {$total} sudah dibaca");

        // Saat kosong, Empty State punya tombolnya sendiri (hanya satu aksi utama).
        $showAdd = !$loadFailed && $total > 0;
    @endphp

    @include('ebooks._topbar', compact('summary', 'showAdd', 'uploadUrl', 'uploadReady'))

    <main class="mx-auto w-full max-w-[1200px] px-4 pb-12 pt-4 sm:px-6">
        <h1 class="text-title-lg font-semibold">Koleksi Ebook Saya</h1>

        {{-- Error ubah status atau validasi --}}
        @if (session('error') || $errors->any())
            <div role="alert"
                class="mt-4 flex animate-enter items-start gap-3 rounded-md bg-primary-tint px-4 py-3 text-error">
                <x-icon.warning class="size-6 shrink-0" aria-hidden="true" />
                <p>{{ session('error') ?? $errors->first() }}</p>
            </div>
        @endif

        @if ($loadFailed)
            {{-- Gagal dimuat: BUKAN Empty State, agar tidak menyiratkan data hilang --}}
            <div role="alert"
                class="mt-4 flex animate-enter items-start gap-3 rounded-md bg-primary-tint px-4 py-3 text-error">
                <x-icon.warning class="size-6 shrink-0" aria-hidden="true" />
                <div class="grid gap-2">
                    <p class="font-medium">Koleksi belum dapat ditampilkan.</p>
                    <p class="text-body-sm">Periksa koneksi Anda, lalu muat ulang halaman.</p>
                    <div>
                        <x-link-button variant="secondary" :href="route('ebooks.index')">Muat Ulang Halaman</x-link-button>
                    </div>
                </div>
            </div>
        @elseif ($total === 0)
            {{-- Empty State (06 bagian 7.7; wireframe Empty State) --}}
            <section
                class="mt-4 grid animate-enter grid-cols-[3rem_1fr] items-start gap-x-3 gap-y-3 rounded-lg bg-primary-tint p-4 sm:p-6 lg:grid-cols-[4rem_1fr] lg:gap-x-4">
                <x-icon.library class="size-12 text-primary lg:row-span-3 lg:size-16" aria-hidden="true" />

                <div class="grid gap-1">
                    <h2 class="text-title-md font-semibold text-text">Koleksi Anda masih kosong</h2>
                    <p class="text-body-sm text-text lg:text-body">
                        Tambahkan PDF pertama Anda agar bisa dibuka dan ditandai dari Web maupun Android.
                    </p>
                </div>

                <x-link-button variant="primary" :href="$uploadUrl" :disabled="!$uploadReady"
                    class="col-span-2 w-full lg:col-span-1 lg:col-start-2 lg:mt-1 lg:w-fit">
                    <x-icon.plus
                        class="size-6 transition-transform duration-200 ease-standard group-hover:rotate-90 motion-reduce:transform-none"
                        aria-hidden="true" />
                    <span class="lg:hidden">Tambah PDF Pertama</span>
                    <span class="hidden lg:inline">Tambah PDF Pertama ke Koleksi</span>
                </x-link-button>

                <p class="col-span-2 text-caption text-text-muted lg:col-span-1 lg:col-start-2">Hanya PDF, maksimal 20
                    MB.</p>
            </section>
        @else
            {{-- Grid kartu: 1 kolom mobile, 2 kolom tablet, auto-fill (min 320) desktop --}}
            <section
                class="grid grid-cols-1 gap-3 py-4 sm:grid-cols-2 lg:grid-cols-[repeat(auto-fill,minmax(320px,1fr))]">
                @foreach ($ebooks as $ebook)
                    <x-card id="ebook-{{ $ebook->id }}" :interactive="$fileReady" :new="request('baru') == $ebook->id" class="animate-enter"
                        style="animation-delay: {{ min($loop->index, 5) * 30 }}ms">

                        <h2 class="text-title-md font-semibold">
                            @if ($fileReady)
                                {{-- Tautan terentang: seluruh kartu membuka PDF (UC-08) --}}
                                <a href="{{ route('ebooks.file', $ebook->id) }}" target="_blank" rel="noopener"
                                    class="line-clamp-2 transition-colors duration-100 ease-standard after:absolute after:inset-0 after:content-[''] group-hover:text-primary">{{ $ebook->title }}</a>
                            @else
                                <span class="line-clamp-2">{{ $ebook->title }}</span>
                            @endif
                        </h2>

                        <p class="text-body-sm text-text-muted">
                            {{ $ebook->created_at->copy()->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('j M Y') }}
                        </p>

                        {{-- Chip dan hapus berada di atas tautan terentang (z-10) --}}
                        <div class="relative z-10 mt-1 flex items-center justify-between gap-2">
                            {{-- Ubah status baca: PATCH /ebooks/{id} -> EbookService::setReadStatus --}}
                            <form method="POST" action="{{ route('ebooks.update', $ebook->id) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_read" value="{{ $ebook->is_read ? 0 : 1 }}">

                                <x-chip :pressed="$ebook->is_read"
                                    aria-label="{{ $ebook->is_read ? 'Sudah dibaca. Tandai belum dibaca.' : 'Belum dibaca. Tandai sudah dibaca.' }}">
                                    @if ($ebook->is_read)
                                        <x-icon.check class="size-4" aria-hidden="true" /> Sudah Dibaca
                                    @else
                                        <x-icon.circle class="size-4" aria-hidden="true" /> Belum Dibaca
                                    @endif
                                </x-chip>
                            </form>

                            {{-- Hapus: belum tersedia (nonaktif, tetap dapat difokus dan dibacakan) --}}
                            <button type="button" aria-disabled="true" aria-label="Hapus ebook, segera hadir"
                                class="inline-grid size-12 cursor-not-allowed place-items-center rounded-full opacity-[0.38] lg:size-10">
                                <x-icon.trash class="size-6" aria-hidden="true" />
                            </button>
                        </div>
                    </x-card>
                @endforeach
            </section>
        @endif
    </main>

    {{-- Snackbar sukses: ink, ikon centang, hilang otomatis 4 detik (06 bagian 7.8; app.js [data-snackbar]) --}}
    @if (session('success'))
        <div data-snackbar class="pointer-events-none fixed inset-x-0 bottom-4 flex justify-center px-4">
            <div role="status"
                class="pointer-events-auto flex animate-enter items-center gap-2 rounded-full bg-ink px-6 py-3 text-body-sm text-on-ink">
                <x-icon.check class="size-5 shrink-0" aria-hidden="true" />
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
</x-layout>
