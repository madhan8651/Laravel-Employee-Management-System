@props([
    'status' => 'Pending',
])

@php
    $normalizedStatus = strtolower(trim($status));

    $statusClass = match ($normalizedStatus) {
        'active', 'approved', 'completed', 'success' => 'status-success',
        'inactive', 'rejected', 'failed', 'error' => 'status-danger',
        'pending', 'processing' => 'status-warning',
        default => 'status-neutral',
    };
@endphp

<span class="status-badge {{ $statusClass }}">
    {{ $status }}
</span>

@push('styles')
<style>
.status-badge {
    display: inline-block;
    padding: .25rem .65rem;
    border-radius: 999px;
    font-size: .85rem;
    font-weight: 600;
}
.status-success { background: #e8f7ed; color: #176534; }
.status-danger { background: #fdecec; color: #9b1c1c; }
.status-warning { background: #fff5d6; color: #795600; }
.status-neutral { background: #eee; color: #444; }
</style>
@endpush
