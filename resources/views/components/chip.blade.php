{{-- Chip status baca (06 bagian 7.4). Gaya "Sudah Dibaca" dipicu data-pressed; nama aksesibel lewat aria-label. --}}
@props(['pressed' => false])
<button type="submit" @if ($pressed) data-pressed @endif
    {{ $attributes->class([
        'inline-flex min-h-12 cursor-pointer items-center gap-2 rounded-full border border-outline-strong bg-transparent px-4 text-label font-medium text-text lg:min-h-9',
        'transition duration-200 ease-standard active:scale-[0.97] motion-reduce:active:scale-100',
        'not-data-[pressed]:hover:border-primary not-data-[pressed]:hover:bg-primary-tint',
        'data-[pressed]:border-ink data-[pressed]:bg-ink data-[pressed]:text-on-ink',
        'data-[pressed]:hover:border-ink-hover data-[pressed]:hover:bg-ink-hover',
    ]) }}>{{ $slot }}</button>
