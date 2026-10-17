{{-- Bandeau "Demander un devis" (appel à l'action cohérent sur toutes les pages) --}}
@php $c = config('hnbloom'); @endphp
<section class="hb-cta reveal">
    <div>
        <p class="hb-eyebrow">VOTRE PROJET</p>
        <h2>{{ $heading ?? 'Parlons de votre projet' }}</h2>
        <p>{{ $text ?? 'Décrivez votre besoin : nos ingénieurs vous proposent une solution adaptée à votre bâtiment.' }}</p>
    </div>
    <div class="hb-cta-actions">
        <a class="hb-button hb-button-gold" href="{{ route('contact', ['sujet' => $subject ?? 'quote']) }}">Demander un devis <x-icon name="arrow-right" :size="15" /></a>
        <a class="hb-button hb-button-outline" href="https://wa.me/{{ $c['contact']['whatsapp'] }}" target="_blank" rel="noreferrer"><x-icon name="message-circle" :size="15" /> WhatsApp</a>
    </div>
</section>
