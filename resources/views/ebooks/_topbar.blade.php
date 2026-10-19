{{--
    Bilah atas (Design System 7.6).
    Variabel dari @include: $summary (string|null), $showAdd (bool), $uploadUrl, $uploadReady.
--}}
<header class="topbar">
    <div class="wrap">
        <span class="text-lg/6 font-semibold">Ebook Storage</span>

        <div class="topbar__actions">
            @if ($summary)
                <span class="topbar__summary hidden sm:block">{{ $summary }}</span>
            @endif

            {{-- Aksi utama satu-satunya di layar daftar (varian inverse di atas bilah merah) --}}
            @if ($showAdd)
                <a href="{{ $uploadUrl }}"
                    @unless ($uploadReady) aria-disabled="true" tabindex="-1" @endunless
                    class="btn btn--inverse btn--plus">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    Tambah PDF
                </a>
            @endif

            <span class="hidden max-w-40 truncate text-sm/5 md:block">{{ auth()->user()->name }}</span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="icon-btn" aria-label="Keluar">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <path d="M16 17l5-5-5-5" />
                        <path d="M21 12H9" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>
