@props(['status'])
@php
    $map = [
        'approved' => ['Approuvé', 'border-green-600/40 text-green-700'],
        'pending' => ['En attente', 'border-gold/60 text-gold-dark'],
        'active' => ['Actif', 'border-green-600/40 text-green-700'],
        'inactive' => ['Inactif', 'border-gray-300 text-stone'],
    ];
    [$label, $classes] = $map[$status] ?? [$status, 'border-gray-300 text-stone'];
@endphp
<span class="inline-block border px-2 py-0.5 text-[10px] uppercase tracking-widest {{ $classes }}">{{ $label }}</span>
