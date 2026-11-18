@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => false,
])

<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>

    <input
        id="{{ $name }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ $type === 'password' ? '' : old($name, $value) }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'form-control']) }}
    >

    @error($name)
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>

@push('styles')
<style>
.form-group {
    margin-bottom: 1rem;
}
.form-group label {
    display: block;
    margin-bottom: .35rem;
}
.form-control {
    box-sizing: border-box;
    width: 100%;
    padding: .65rem;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.field-error {
    color: #b42318;
    font-size: .9rem;
}
</style>
@endpush
