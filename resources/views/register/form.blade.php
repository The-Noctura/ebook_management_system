<x-auth-layout title="Daftar - {{ config('app.name') }}">
    <h1 class="text-title-lg font-semibold">Buat Akun Baru</h1>

    <form action="{{ route('register.store') }}" method="POST" class="mt-6 grid gap-4">
        @csrf
        <x-field name="name" label="Nama" placeholder="Ketik nama Anda" autocomplete="name" required autofocus />
        <x-field name="email" label="Email" type="email" placeholder="Ketik email aktif Anda" autocomplete="email"
            required />
        <x-field name="password" label="Password" type="password" placeholder="Buat password baru"
            hint="Minimal 8 karakter." autocomplete="new-password" required />
        <x-field name="password_confirmation" label="Konfirmasi Password" type="password"
            placeholder="Ketik ulang password Anda" autocomplete="new-password" required />
        <x-button type="submit" class="w-full">Buat Akun Saya</x-button>
    </form>

    <p class="mt-6 text-body-sm text-text-muted">
        Sudah punya akun?
        <a href="{{ route('login') }}"
            class="text-primary underline decoration-transparent underline-offset-4 transition-colors duration-100 hover:decoration-current">Masuk
            di sini</a>
    </p>
</x-auth-layout>
