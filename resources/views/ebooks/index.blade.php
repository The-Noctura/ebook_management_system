<x-layout bare title="Koleksi Ebook Saya - {{ config('app.name', 'Laravel') }}">
    @php
        // Route upload dan buka PDF dibuat di C11. Sebelum itu, tombol tampil nonaktif
        // dan kartu belum dapat ditekan.
        $uploadReady = Route::has('ebooks.create');
        $fileReady = Route::has('ebooks.file');
        $uploadUrl = $uploadReady ? route('ebooks.create') : '#';

        $total = $ebooks->count();
        $summary = $loadFailed ? null : ($total === 0 ? 'Belum ada ebook' : "{$readCount} dari {$total} sudah dibaca");

        // Tombol Tambah PDF di bilah atas hanya saat ada ebook (Empty State punya tombolnya sendiri,
        // supaya tidak ada dua aksi utama).
        $showAdd = !$loadFailed && $total > 0;
    @endphp

    @include('ebooks._topbar', compact('summary', 'showAdd', 'uploadUrl', 'uploadReady'))

    <main class="wrap pt-4 pb-12">
        <h1 class="text-2xl/8 font-semibold">Koleksi Ebook Saya</h1>

        {{-- Ringkasan untuk layar kecil (di layar lebar ada di bilah atas) --}}
        @if ($summary)
            <p class="text-sm/5 text-muted sm:hidden">{{ $summary }}</p>
        @endif

        {{-- Error ubah status atau validasi --}}
        @if (session('error') || $errors->any())
            <div role="alert" class="notice mt-4">
                <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 8v4M12 16h.01" />
                </svg>
                <p class="notice__message">{{ session('error') ?? $errors->first() }}</p>
            </div>
        @endif

        @if ($loadFailed)
            {{-- Gagal dimuat: BUKAN Empty State, agar tidak menyiratkan data hilang --}}
            <div role="alert" class="notice mt-4">
                <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 8v4M12 16h.01" />
                </svg>
                <p class="notice__message">Koleksi belum dapat ditampilkan.</p>
                <a href="{{ route('ebooks.index') }}" class="btn btn--secondary">Muat Ulang Halaman</a>
            </div>
        @elseif ($total === 0)
            {{-- Empty State (Design System 7.7): kotak tint, ikon, satu kalimat, satu tombol --}}
            <section class="empty mt-4">
                <svg class="empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z" />
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                </svg>

                <div class="empty__body">
                    <p>Koleksi Anda masih kosong. Tambahkan PDF pertama Anda agar bisa dibuka dan ditandai dari Web
                        maupun Android.</p>

                    <a href="{{ $uploadUrl }}"
                        @unless ($uploadReady) aria-disabled="true" tabindex="-1" @endunless
                        class="btn btn--primary btn--plus">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        Tambah PDF Pertama ke Koleksi
                    </a>

                    <p class="text-xs/4 text-muted">Hanya PDF, maksimal 20 MB.</p>
                </div>
            </section>
        @else
            {{-- Daftar kartu ebook (Design System 7.3) --}}
            <ul class="list">
                @foreach ($ebooks as $ebook)
                    <li id="ebook-{{ $ebook->id }}" class="card {{ $fileReady ? 'card--link' : '' }}"
                        style="animation-delay: {{ min($loop->index, 5) * 30 }}ms">

                        <h2 class="card__title line-clamp-2">
                            @if ($fileReady)
                                {{-- Tautan terentang: seluruh kartu membuka PDF (UC-08) --}}
                                <a href="{{ route('ebooks.file', $ebook->id) }}"
                                    class="card__link">{{ $ebook->title }}</a>
                            @else
                                {{ $ebook->title }}
                            @endif
                        </h2>

                        <p class="card__meta">
                            {{ $ebook->created_at->copy()->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('j M Y') }}
                        </p>

                        {{-- Ubah status baca: PATCH /ebooks/{id} -> EbookService::setReadStatus --}}
                        <form method="POST" action="{{ route('ebooks.update', $ebook->id) }}"
                            class="card__action mt-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_read" value="{{ $ebook->is_read ? 0 : 1 }}">

                            <button type="submit" class="chip"
                                aria-pressed="{{ $ebook->is_read ? 'true' : 'false' }}">
                                @if ($ebook->is_read)
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        aria-hidden="true">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
                                    Sudah Dibaca
                                @else
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9" />
                                    </svg>
                                    Belum Dibaca
                                @endif
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    </main>

    {{-- Snackbar sukses: ink, ikon centang, hilang otomatis 4 detik (Design System 7.8) --}}
    @if (session('success'))
        <div role="status" class="snackbar">
            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
</x-layout>
