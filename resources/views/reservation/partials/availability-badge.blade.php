@props(['status', 'available_slots'])

@php
    $badgeClasses = [
        'complet' => 'bg-danger',
        'presque_complet' => 'bg-warning',
        'disponible' => 'bg-success'
    ];
@endphp

<span class="badge {{ $badgeClasses[$status] ?? 'bg-secondary' }}">
    {{ $available_slots }} places - {{ ucfirst($status) }}
</span>