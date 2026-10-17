@php $c = config('hnbloom'); @endphp
<footer class="hb-footer">
    <a href="{{ route('home') }}" class="hb-footer-brand">H<span>H</span> BLOOM</a>
    <p>© {{ date('Y') }} {{ $c['name'] }}. Tous droits réservés.</p>
    <div class="hb-socials">
        @foreach($c['contact']['socials'] as $s)
            <a href="{{ $s['href'] }}" target="_blank" rel="noreferrer">{{ $s['label'] }}</a>
        @endforeach
    </div>
</footer>
