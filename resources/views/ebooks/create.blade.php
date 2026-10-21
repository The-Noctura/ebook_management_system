<x-layout bare title="Tambah PDF - {{ config('app.name', 'Laravel') }}">
    {{-- Bilah atas tanpa Tambah PDF dan tanpa ringkasan (wireframe Upload bagian 1.2) --}}
    @include('ebooks._topbar', [
        'summary' => null,
        'showAdd' => false,
        'uploadUrl' => '#',
        'uploadReady' => false,
        'homeUrl' => route('ebooks.index'),
    ])

    @php $fileError = $errors->first('file'); @endphp

    <main class="mx-auto w-full max-w-[1200px] px-4 pb-12 pt-4 sm:px-6">
        <h1 class="text-title-lg font-semibold">Tambah PDF ke Koleksi</h1>

        <div class="grid gap-6 py-4 lg:grid-cols-[minmax(0,1fr)_360px]">

            {{-- Kotak aturan: di atas pada mobile, di kanan pada desktop --}}
            <aside class="animate-enter rounded-lg bg-primary-tint p-4 lg:order-2 lg:self-start lg:p-6">
                <h2 class="flex items-center gap-2 text-title-md font-semibold text-text">
                    <x-icon.info class="size-6 text-primary" aria-hidden="true" /> Aturan upload
                </h2>
                <ul class="mt-2 grid list-disc gap-1 pl-6 text-body text-text">
                    <li>Hanya PDF, maksimal 20 MB.</li>
                    <li>PDF yang sama tidak ditambahkan dua kali.</li>
                    <li>Upload membutuhkan koneksi internet.</li>
                </ul>
            </aside>

            <form method="POST" action="{{ route('ebooks.store') }}" enctype="multipart/form-data" novalidate
                data-upload-form class="grid max-w-xl animate-enter content-start gap-4">
                @csrf

                {{-- Gagal di server (500) --}}
                @if (session('upload_failed'))
                    <div role="alert"
                        class="flex animate-enter items-start gap-3 rounded-md bg-primary-tint px-4 py-3 text-error">
                        <x-icon.warning class="size-6 shrink-0" aria-hidden="true" />
                        <div class="grid gap-2">
                            <p>PDF belum bisa ditambahkan. Silakan coba lagi.</p>
                            <div>
                                <x-button variant="secondary" type="button" data-focus="file">Coba Lagi</x-button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Tanpa koneksi: ditampilkan oleh JavaScript saat submit --}}
                <div role="alert" data-offline-alert hidden
                    class="flex items-start gap-3 rounded-md bg-primary-tint px-4 py-3 text-error">
                    <x-icon.warning class="size-6 shrink-0" aria-hidden="true" />
                    <p>Tidak ada koneksi. Coba lagi saat online.</p>
                </div>

                {{-- Kolom file --}}
                <div class="grid gap-1">
                    <label for="file" class="text-label font-medium">File PDF</label>
                    <input id="file" name="file" type="file" accept="application/pdf,.pdf"
                        aria-describedby="file-hint{{ $fileError ? ' file-msg' : '' }}"
                        @if ($fileError) aria-invalid="true" @endif
                        class="{{ $fileError ? 'animate-shake border-error' : 'border-outline-strong hover:border-ink' }} min-h-14 w-full rounded-md border bg-background px-2 text-body text-text transition-colors duration-200 ease-standard focus:border-primary lg:min-h-11 file:mr-4 file:min-h-10 file:cursor-pointer file:rounded-full file:border file:border-outline-strong file:bg-transparent file:px-5 file:text-label file:font-medium file:text-text file:transition file:duration-100 file:ease-standard hover:file:bg-ink hover:file:text-on-ink">

                    {{-- Error dari server --}}
                    @if ($fileError)
                        <p id="file-msg"
                            class="flex animate-enter flex-wrap items-start gap-1 text-caption text-error">
                            <x-icon.warning class="size-4 shrink-0" aria-hidden="true" />
                            <span>{{ $fileError }}</span>
                            @if (session('existing_id'))
                                <a href="{{ route('ebooks.index') }}#ebook-{{ session('existing_id') }}"
                                    class="ml-1 text-primary underline underline-offset-4">Lihat di koleksi</a>
                            @endif
                        </p>
                    @endif

                    {{-- Error dari JavaScript (file di atas 20 MB) --}}
                    <p data-file-error role="alert" hidden
                        class="flex animate-enter items-start gap-1 text-caption text-error">
                        <x-icon.warning class="size-4 shrink-0" aria-hidden="true" />
                        <span data-file-error-text></span>
                    </p>

                    <p id="file-hint" class="text-caption text-text-muted">Hanya PDF, maksimal 20 MB.</p>
                </div>

                <x-field name="title" label="Judul (opsional)" placeholder="Kosongkan untuk memakai nama file" />

                <div class="flex flex-col gap-2 pt-2 sm:flex-row">
                    <x-button variant="primary" type="submit" class="w-full sm:w-auto"
                        data-loading="Mengunggah…">Tambahkan ke Koleksi</x-button>
                    <x-button variant="secondary" :href="route('ebooks.index')" class="w-full sm:w-auto">Batal</x-button>
                </div>
            </form>
        </div>
    </main>
</x-layout>
