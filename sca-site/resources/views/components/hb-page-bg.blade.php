@props(['image' => null])
@php
    $cfg = config('hnbloom');
    $bg = collect([
        $image,
        $cfg['images']['solutions'] ?? null,
        $cfg['images']['hero'] ?? null,
    ])->first(fn ($p) => $p && file_exists(public_path($p)));
@endphp

@if($bg)
    <div class="hb-catalog-bg" style="background-image:url('{{ asset($bg) }}')" aria-hidden="true"></div>
@endif
