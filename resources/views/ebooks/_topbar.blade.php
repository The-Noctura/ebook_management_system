{{--
    Bilah atas (dokumen 06 bagian 7.6; wireframe Daftar Ebook bagian 8).
    Variabel: $summary (string|null), $showAdd (bool), $uploadUrl, $uploadReady.
    Di bawah 1024 px ringkasan berada di bawah nama aplikasi; di desktop di kanan.
--}}
<header class="topbar bg-primary text-on-primary">
    <div class="mx-auto flex min-h-16 w-full max-w-[1200px] items-center gap-3 px-4 sm:px-6 lg:min-h-14">
        <div class="grid min-w-0 flex-1 leading-tight">
            <span class="text-title-md font-semibold">Ebook Storage</span>
            @if ($summary)
                <span class="text-body-sm lg:hidden">{{ $summary }}</span>
            @endif
        </div>

        @if ($summary)
            <span class="hidden text-body-sm lg:block">{{ $summary }}</span>
        @endif

        {{-- Satu-satunya aksi utama di layar daftar --}}
        @if ($showAdd)
            <x-link-button variant="inverse" :href="$uploadUrl" :disabled="!$uploadReady" aria-label="Tambah PDF">
                <x-icon.plus
                    class="size-6 transition-transform duration-200 ease-standard group-hover:rotate-90 motion-reduce:transform-none"
                    aria-hidden="true" />
                <span class="hidden min-[420px]:inline">Tambah PDF</span>
            </x-link-button>
        @endif

        <span class="hidden max-w-40 truncate text-body-sm lg:block">{{ auth()->user()->name }}</span>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" aria-label="Keluar"
                class="inline-grid size-10 cursor-pointer place-items-center rounded-full transition duration-100 ease-standard hover:bg-white/15 active:scale-95 motion-reduce:active:scale-100">
                <x-icon.logout class="size-6" aria-hidden="true" />
            </button>
        </form>
    </div>
</header>
