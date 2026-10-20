@props(['name', 'label', 'type' => 'text', 'hint' => null])
@php
    $error = $errors->first($name);
    $isPassword = $type === 'password';
@endphp
<div class="grid gap-1">
    <label for="{{ $name }}" class="text-label font-medium">{{ $label }}</label>
    <div class="relative">
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
            value="{{ $isPassword ? '' : old($name) }}"
            @if ($error) aria-invalid="true" aria-describedby="{{ $name }}-msg" @endif
            {{ $attributes->class([
                'w-full min-h-14 rounded-md border bg-background px-4 text-body text-text lg:min-h-11',
                'pr-14' => $isPassword,
                'transition-colors duration-200 ease-standard focus:border-primary',
                $error ? 'animate-shake border-error' : 'border-outline-strong not-focus:hover:border-ink',
            ]) }}>
        @if ($isPassword)
            <button type="button" data-toggle-password aria-label="Tampilkan password" aria-pressed="false"
                class="absolute right-2 top-1/2 inline-grid size-10 -translate-y-1/2 cursor-pointer place-items-center rounded-full text-text-muted transition duration-100 ease-standard hover:bg-primary-tint active:scale-95 motion-reduce:active:scale-100">
                <x-icon.eye class="size-6" data-icon-show />
                <x-icon.eye-off class="hidden size-6" data-icon-hide />
            </button>
        @endif
    </div>
    @if ($error)
        <p id="{{ $name }}-msg" class="flex animate-enter items-start gap-1 text-caption text-error">
            <x-icon.warning class="size-4 shrink-0" /> {{ $error }}
        </p>
    @elseif ($hint)
        <p class="text-caption text-text-muted">{{ $hint }}</p>
    @endif
</div>
