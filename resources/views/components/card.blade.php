{{-- Kartu ebook (dokumen 06 bagian 7.3 dan 10.4). `interactive` = seluruh kartu dapat dibuka (stretched link). --}}
@props(['new' => false, 'interactive' => true])
<article
    {{ $attributes->class([
        'group relative grid gap-2 rounded-lg border border-outline bg-surface p-4 lg:p-3',
        'transition duration-200 ease-standard data-[new]:bg-primary-tint',
        'cursor-pointer hover:-translate-y-0.5 hover:border-primary active:scale-[0.98] motion-reduce:hover:translate-y-0 motion-reduce:active:scale-100' => $interactive,
    ]) }}
    @if ($new) data-new @endif>
    {{ $slot }}
</article>
