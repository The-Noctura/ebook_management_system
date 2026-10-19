@props(['name', 'label', 'type' => 'text', 'hint' => null])
@php $error = $errors->first($name); @endphp
<div class="grid gap-1">
    <label for="{{ $name }}" class="text-label font-medium">{{ $label }}</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
        value="{{ $type === 'password' ? '' : old($name) }}"
        @if ($error) aria-invalid="true" aria-describedby="{{ $name }}-msg" @endif
        {{ $attributes->class([
            'min-h-14 rounded-md border bg-background px-4 text-body text-text lg:min-h-11',
            'transition-colors duration-200 ease-standard focus:border-primary',
            $error ? 'animate-shake border-error' : 'border-outline-strong not-focus:hover:border-ink',
        ]) }}>
    @if ($error)
        <p id="{{ $name }}-msg" class="flex animate-enter items-start gap-1 text-caption text-error">
            <x-icon.warning class="size-4 shrink-0" /> {{ $error }}
        </p>
    @elseif ($hint)
        <p class="text-caption text-text-muted">{{ $hint }}</p>
    @endif
</div>
