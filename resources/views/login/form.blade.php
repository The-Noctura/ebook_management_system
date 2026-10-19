<x-auth-layout title="Masuk - {{ config('app.name') }}">
    <h1 class="text-title-lg font-semibold">Masuk ke Koleksi Anda</h1>

    @if (session('success') || session('status'))
        <div class="mt-4 flex animate-enter items-start gap-2 rounded-md bg-ink px-4 py-3 text-body-sm text-on-ink">
            <x-icon.check class="size-5 shrink-0" />
            <span>{{ session('success') ?? session('status') }}</span>
        </div>
    @endif

    <form action="{{ route('login.store') }}" method="POST" class="mt-6 grid gap-4">
        @csrf
        <x-field name="email" label="Email" type="email" placeholder="Ketik email akun Anda" autocomplete="email"
            required autofocus />
        <x-field name="password" label="Password" type="password" placeholder="Masukkan password Anda"
            autocomplete="current-password" required />
        <x-button type="submit" class="w-full">Masuk ke Koleksi Ebook</x-button>
    </form>

    <p class="mt-6 text-body-sm text-text-muted">
        Belum punya akun?
        <a href="{{ route('register.form') }}"
            class="text-primary underline decoration-transparent underline-offset-4 transition-colors duration-100 hover:decoration-current">Buat
            akun baru</a>
    </p>
</x-auth-layout>
