@props(['variant' => 'primary', 'type' => 'button', 'href' => null])
@php
    $variants = [
        'primary' => 'border-transparent bg-primary text-on-primary hover:bg-primary-hover active:bg-primary-pressed',
        'secondary' => 'border-outline-strong bg-transparent text-text hover:border-ink hover:bg-ink hover:text-on-ink',
        'text' => 'border-transparent bg-transparent text-primary hover:bg-primary-tint',
        'inverse' => 'border-transparent bg-white text-primary hover:bg-primary-tint',
    ];
    $classes =
        'group inline-flex min-h-12 items-center justify-center gap-2 rounded-full border px-6 text-label font-medium lg:min-h-10 lg:px-5 ' .
        'cursor-pointer transition duration-100 ease-standard active:scale-[0.97] motion-reduce:active:scale-100 ' .
        'disabled:cursor-not-allowed disabled:opacity-[0.38] disabled:active:scale-100 ' .
        $variants[$variant];
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->class([$classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class([$classes]) }}>{{ $slot }}</button>
@endif
