@props(['pressed' => false])
<button type="submit" aria-pressed="{{ $pressed ? 'true' : 'false' }}"
    {{ $attributes->class([
        'inline-flex min-h-12 cursor-pointer items-center gap-2 rounded-full border border-outline-strong bg-transparent px-4 text-label font-medium text-text lg:min-h-9',
        'transition duration-200 ease-standard active:scale-[0.97] motion-reduce:active:scale-100',
        'not-aria-pressed:hover:border-primary not-aria-pressed:hover:bg-primary-tint',
        'aria-pressed:border-ink aria-pressed:bg-ink aria-pressed:text-on-ink',
        'aria-pressed:hover:border-ink-hover aria-pressed:hover:bg-ink-hover',
    ]) }}>{{ $slot }}</button>
