@props([
    'type' => 'success',
    'message' => null,
])

@if ($message)
    <div class="alert alert-{{ $type }}" role="alert">
        {{ $message }}
    </div>
@endif

@push('styles')
<style>
.alert {
    padding: 12px 16px;
    margin: 12px 0;
    border: 1px solid #ddd;
    border-radius: 6px;
}
.alert-success {
    background: #e8f7ed;
    color: #176534;
}
.alert-error {
    background: #fdecec;
    color: #9b1c1c;
}
.alert-warning {
    background: #fff5d6;
    color: #795600;
}
</style>
@endpush
