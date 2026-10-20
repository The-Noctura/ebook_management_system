<x-auth-layout title="Masuk - {{ config('app.name') }}">
    <h1 class="text-title-lg font-semibold">Masuk ke Koleksi Anda</h1>

    {{-- Snackbar sukses (setelah register), hilang otomatis 4 detik --}}
    @if (session('status'))
        <div data-snackbar class="pointer-events-none fixed inset-x-0 bottom-6 z-10 flex justify-center px-4">
            <div role="status"
                class="flex animate-enter items-center gap-2 rounded-full bg-ink px-6 py-3 text-body-sm text-on-ink">
                <x-icon.check class="size-5 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    {{-- Kredensial salah / terlalu banyak percobaan --}}
    @error('credentials')
        <div role="alert"
            class="mt-4 flex animate-enter items-start gap-2 rounded-md bg-primary-tint px-4 py-3 text-body-sm text-error">
            <x-icon.warning class="size-5 shrink-0" />
            <span>{{ $message }}</span>
        </div>
    @enderror

    {{-- novalidate: pesan kolom kosong datang dari server (bahasa Indonesia), bukan balon browser --}}
    <form action="{{ route('login.store') }}" method="POST" novalidate class="mt-6 grid gap-4">
        @csrf
        <x-field name="email" label="Email" type="email" placeholder="Ketik email akun Anda" autocomplete="email"
            autofocus />
        <x-field name="password" label="Password" type="password" placeholder="Masukkan password Anda"
            autocomplete="current-password" />
        <x-button type="submit" class="w-full" data-loading="Memeriksa akun…">Masuk ke Koleksi Ebook</x-button>
    </form>

    <p class="mt-6 text-body-sm text-text-muted">
        Belum punya akun?
        <a href="{{ route('register.form') }}"
            class="text-primary underline decoration-transparent underline-offset-4 transition-colors duration-100 hover:decoration-current">Buat
            akun baru</a>
    </p>
</x-auth-layout>
