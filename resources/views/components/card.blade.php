@props(['new' => false])
<article
    {{ $attributes->class([
        'group grid cursor-pointer gap-2 rounded-lg border border-outline bg-surface p-4 lg:p-3',
        'transition duration-200 ease-standard',
        'hover:-translate-y-0.5 hover:border-primary active:scale-[0.98]',
        'motion-reduce:hover:translate-y-0 motion-reduce:active:scale-100',
        'data-[new]:bg-primary-tint',
    ]) }}
    @if ($new) data-new @endif>
    {{ $slot }}
</article>
