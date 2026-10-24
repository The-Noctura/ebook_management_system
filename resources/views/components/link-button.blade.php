{{-- Tautan bergaya tombol (pil). Varian: primary | secondary | inverse | text. Pakai `disabled` bila tujuan belum ada. --}}
@props(['variant' => 'primary', 'href' => '#', 'disabled' => false])
@php
    $variants = [
        'primary' => 'border-transparent bg-primary text-on-primary hover:bg-primary-hover active:bg-primary-pressed',
        'secondary' => 'border-outline-strong bg-transparent text-text hover:border-ink hover:bg-ink hover:text-on-ink',
        'inverse' => 'border-transparent bg-white text-primary hover:bg-primary-tint',
        'text' => 'border-transparent bg-transparent text-primary hover:bg-primary-tint',
    ];
@endphp
<a {{ $disabled ? '' : "href=$href" }}
    @if ($disabled) role="link" aria-disabled="true" tabindex="-1" @endif
    {{ $attributes->class([
        'group inline-flex min-h-12 items-center justify-center gap-2 rounded-full border px-6 text-label font-medium lg:min-h-10 lg:px-5',
        'transition duration-100 ease-standard active:scale-[0.97] motion-reduce:active:scale-100',
        $disabled ? 'pointer-events-none cursor-not-allowed opacity-[0.38]' : 'cursor-pointer',
        $variants[$variant],
    ]) }}>{{ $slot }}</a>
