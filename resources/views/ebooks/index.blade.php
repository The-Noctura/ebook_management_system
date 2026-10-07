<x-layout title="Beranda - {{ config('app.name', 'Laravel') }}">

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

    @foreach ($users as $user)
        <h1>Selamat datang di beranda Ebooks {{ $user->name }}</h1>
    @endforeach

</x-layout>
