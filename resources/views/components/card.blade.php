@props([
    'title' => null,
])

<div {{ $attributes->merge(['class' => 'content-card']) }}>
    @if ($title)
        <h2 class="content-card-title">{{ $title }}</h2>
    @endif

    <div class="content-card-body">
        {{ $slot }}
    </div>
</div>

@push('styles')
<style>
.content-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}
.content-card-title {
    margin-top: 0;
}
.content-card-body {
    line-height: 1.6;
}
</style>
@endpush
