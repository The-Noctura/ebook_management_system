<x-layout bare title="EMS - Simpan dan Kelola PDF Anda">
    {{-- Navbar melayang. h-0 + sticky: tetap terlihat saat gulir, hero berada di belakangnya.
         Dibedakan dari latar oleh garis 1px, bukan bayangan (dokumen 06 bagian 5.3). --}}
    <header class="sticky top-4 z-20 mx-4 h-0 sm:mx-6">
        <nav aria-label="Navigasi utama"
            class="mx-auto grid min-h-14 w-full max-w-[1200px] grid-cols-[1fr_auto_1fr] items-center rounded-full border border-outline bg-white px-4 py-1">
            <a href="{{ route('home') }}" class="justify-self-start text-title-md font-semibold text-primary">EMS</a>

            <ul class="hidden items-center gap-1 lg:flex">
                @foreach ([['#tentang', 'Tentang'], ['#keunggulan', 'Keunggulan'], ['#cara-pakai', 'Cara Pakai']] as [$href, $label])
                    <li>
                        <a href="{{ $href }}"
                            class="inline-flex min-h-10 items-center rounded-full px-4 text-label font-medium text-text transition duration-100 ease-standard hover:bg-primary-tint hover:text-primary active:scale-[0.97] motion-reduce:active:scale-100">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>

            <div class="col-start-3 flex items-center gap-2 justify-self-end">
                <x-link-button variant="text" :href="route('login')">Masuk</x-link-button>
                <x-link-button variant="secondary" :href="route('register.form')">Daftar</x-link-button>
            </div>
        </nav>
    </header>

    <main>
        {{-- HERO: satu-satunya blok merah pekat. Class topbar membuat cincin fokus putih (app.css). --}}
        <section class="topbar bg-primary text-on-primary">
            <div
                class="mx-auto grid w-full max-w-[1200px] animate-enter justify-items-center gap-4 px-4 pb-16 pt-24 text-center sm:px-6 lg:pb-24 lg:pt-32">
                <h1 class="max-w-2xl text-title-lg font-semibold">Semua PDF Anda, Rapi di Satu Tempat</h1>
                <p class="max-w-xl text-body">Unggah ebook PDF, buka dari browser mana pun, dan tandai mana yang sudah
                    selesai Anda baca.</p>
                <x-link-button variant="inverse" :href="route('register.form')" class="mt-2 w-full sm:w-auto">Mulai Secara
                    Gratis</x-link-button>
            </div>
        </section>

        {{-- TENTANG --}}
        <section id="tentang" class="scroll-mt-24 bg-background py-12">
            <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6">
                <h2 class="text-center text-title-lg font-semibold">Apa Itu EMS?</h2>
                <div class="mt-8 grid items-center gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
                    <div class="grid max-w-xl gap-4 text-body">
                        <p>EMS (Ebook Management System) adalah tempat penyimpanan ebook pribadi berbasis web. Anda
                            mengunggah PDF sekali, lalu koleksi itu tersimpan di akun Anda dan bisa dibuka kapan saja
                            lewat browser.</p>
                        <p>Ebook sering tersebar di banyak folder dan perangkat, dan lama-lama sulit diingat mana yang
                            sudah dibaca. EMS mengumpulkannya dalam satu daftar yang jelas, lengkap dengan status baca
                            untuk setiap PDF.</p>
                    </div>
                    <div class="grid justify-items-center gap-2 rounded-lg bg-primary-tint p-6 text-center">
                        <x-icon.library class="size-16 text-primary" aria-hidden="true" />
                        <p class="text-title-md font-semibold">Satu koleksi.</p>
                        <p class="text-title-md font-semibold">Satu akun.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- KEUNGGULAN: latar surface, kartu berlatar background --}}
        <section id="keunggulan" class="scroll-mt-24 bg-surface py-12">
            <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6">
                <h2 class="text-center text-title-lg font-semibold">Kenapa Memakai EMS?</h2>
                <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <article
                        class="grid content-start gap-2 rounded-lg border border-outline bg-background p-4 transition-colors duration-200 ease-standard hover:border-primary lg:p-3">
                        <x-icon.library class="size-6 text-primary" aria-hidden="true" />
                        <h3 class="text-title-md font-semibold">Koleksi terpusat</h3>
                        <p class="text-body-sm text-text-muted">Semua PDF Anda ada dalam satu daftar, diurutkan dari
                            yang terbaru.</p>
                    </article>
                    <article
                        class="grid content-start gap-2 rounded-lg border border-outline bg-background p-4 transition-colors duration-200 ease-standard hover:border-primary lg:p-3">
                        <x-icon.document class="size-6 text-primary" aria-hidden="true" />
                        <h3 class="text-title-md font-semibold">Buka dari browser</h3>
                        <p class="text-body-sm text-text-muted">Tidak perlu memasang aplikasi. PDF langsung terbuka di
                            penampil bawaan browser Anda.</p>
                    </article>
                    <article
                        class="grid content-start gap-2 rounded-lg border border-outline bg-background p-4 transition-colors duration-200 ease-standard hover:border-primary lg:p-3">
                        <x-icon.check class="size-6 text-primary" aria-hidden="true" />
                        <h3 class="text-title-md font-semibold">Status baca yang jelas</h3>
                        <p class="text-body-sm text-text-muted">Tandai PDF sebagai sudah atau belum dibaca, dan lihat
                            ringkasannya di bagian atas koleksi.</p>
                    </article>
                    <article
                        class="grid content-start gap-2 rounded-lg border border-outline bg-background p-4 transition-colors duration-200 ease-standard hover:border-primary lg:p-3">
                        <x-icon.info class="size-6 text-primary" aria-hidden="true" />
                        <h3 class="text-title-md font-semibold">Privat dan aman</h3>
                        <p class="text-body-sm text-text-muted">File hanya bisa dibuka oleh pemilik akun. Pengguna lain
                            tidak bisa melihat koleksi Anda.</p>
                    </article>
                </div>
            </div>
        </section>

        {{-- CARA PAKAI: garis vertikal di mobile, horizontal di desktop --}}
        <section id="cara-pakai" class="scroll-mt-24 bg-background py-12">
            <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6">
                <h2 class="text-center text-title-lg font-semibold">Cara Memakai EMS</h2>
                <ol class="relative mt-8 grid gap-6 lg:grid-cols-3">
                    <div aria-hidden="true" class="absolute inset-x-1/6 top-5 hidden h-px bg-outline lg:block"></div>
                    @foreach ([['Buat akun', 'Daftar dengan nama, email, dan password minimal 8 karakter.'], ['Unggah PDF', 'Pilih file PDF dari perangkat Anda. Ukuran maksimal 20 MB per file.'], ['Baca dan tandai', 'Buka PDF di browser, lalu tandai sebagai sudah atau belum dibaca.']] as $i => [$judul, $isi])
                        <li
                            class="relative flex gap-4 max-lg:not-last:after:absolute max-lg:not-last:after:-bottom-6 max-lg:not-last:after:left-5 max-lg:not-last:after:top-10 max-lg:not-last:after:w-px max-lg:not-last:after:bg-outline lg:grid lg:justify-items-center lg:text-center">
                            <span
                                class="relative z-10 inline-grid size-10 shrink-0 place-items-center rounded-full bg-primary text-label font-medium text-on-primary">{{ $i + 1 }}</span>
                            <div class="grid gap-1">
                                <h3 class="text-title-md font-semibold">{{ $judul }}</h3>
                                <p class="text-body-sm text-text-muted">{{ $isi }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        {{-- FAQ: <details> bawaan HTML, tanpa JavaScript --}}
        <section id="faq" class="scroll-mt-24 bg-surface py-12">
            <div class="mx-auto w-full max-w-[720px] px-4 sm:px-6">
                <h2 class="text-center text-title-lg font-semibold">Pertanyaan yang Sering Muncul</h2>
                <div
                    class="mt-8 divide-y divide-outline overflow-hidden rounded-lg border border-outline bg-background">
                    @foreach ([['Format file apa yang didukung?', 'Saat ini hanya PDF, dengan ukuran maksimal 20 MB per file.'], ['Apakah file saya bisa dilihat pengguna lain?', 'Tidak. Setiap PDF hanya bisa dibuka oleh pemilik akun yang mengunggahnya.'], ['Bagaimana jika saya mengunggah PDF yang sama dua kali?', 'Sistem mengenali isi file, bukan namanya. PDF yang sama dengan yang sudah ada di koleksi Anda akan ditolak, jadi koleksi tidak terisi duplikat.'], ['Apakah bisa dibuka dari HP?', 'Bisa, lewat browser di HP. Saat ini belum ada aplikasi Android.']] as [$tanya, $jawab])
                        <details class="group">
                            <summary
                                class="flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-3 text-label font-medium transition-colors duration-200 ease-standard hover:bg-primary-tint [&::-webkit-details-marker]:hidden">
                                <span>{{ $tanya }}</span>
                                <x-icon.plus class="size-6 shrink-0 text-primary group-open:hidden"
                                    aria-hidden="true" />
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"
                                    class="hidden size-6 shrink-0 text-primary group-open:block">
                                    <path d="M19 13H5v-2h14v2z" />
                                </svg>
                            </summary>
                            <p class="px-4 pb-4 text-body-sm text-text-muted">{{ $jawab }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CTA PENUTUP: primary di atas kotak primary-tint, bukan di atas blok merah --}}
        <section class="bg-background py-12">
            <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6">
                <div class="grid justify-items-center gap-3 rounded-lg bg-primary-tint p-6 text-center">
                    <h2 class="text-title-lg font-semibold">Siap merapikan koleksi PDF Anda?</h2>
                    <p class="text-body">Buat akun dalam satu menit.</p>
                    <x-link-button variant="primary" :href="route('register.form')" class="mt-1 w-full sm:w-auto">Buat Akun
                        Sekarang</x-link-button>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-outline bg-surface">
        <div class="mx-auto grid w-full max-w-[1200px] gap-6 px-4 py-8 sm:grid-cols-3 sm:px-6">
            <div class="grid content-start gap-1">
                <p class="text-title-md font-semibold text-primary">EMS</p>
                <p class="text-body-sm text-text-muted">Ebook Management System. Simpan dan kelola PDF pribadi Anda.</p>
            </div>
            <nav aria-label="Navigasi footer" class="grid content-start gap-1">
                <p class="text-label font-medium">Navigasi</p>
                @foreach ([['#tentang', 'Tentang'], ['#keunggulan', 'Keunggulan'], ['#cara-pakai', 'Cara Pakai'], ['#faq', 'Pertanyaan']] as [$href, $label])
                    <a href="{{ $href }}"
                        class="w-fit text-body-sm text-text-muted underline decoration-transparent underline-offset-4 transition-colors duration-100 hover:text-primary hover:decoration-current">{{ $label }}</a>
                @endforeach
            </nav>
            <nav aria-label="Akun" class="grid content-start gap-1">
                <p class="text-label font-medium">Akun</p>
                <a href="{{ route('login') }}"
                    class="w-fit text-body-sm text-text-muted underline decoration-transparent underline-offset-4 transition-colors duration-100 hover:text-primary hover:decoration-current">Masuk</a>
                <a href="{{ route('register.form') }}"
                    class="w-fit text-body-sm text-text-muted underline decoration-transparent underline-offset-4 transition-colors duration-100 hover:text-primary hover:decoration-current">Buat
                    Akun</a>
            </nav>
        </div>
        <div class="border-t border-outline">
            <p class="mx-auto w-full max-w-[1200px] px-4 py-4 text-body-sm text-text-muted sm:px-6">© 2026 EMS. Dibuat
                oleh The-Noctura.</p>
        </div>
    </footer>
</x-layout>
