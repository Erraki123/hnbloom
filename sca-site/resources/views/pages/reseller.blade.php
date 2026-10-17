<x-hb-layout title="Espace revendeur" description="Espace réservé aux revendeurs et professionnels : catalogue avec tarifs, liste de commande et suivi.">

    <section class="hb-page-hero">
        <div class="hb-wrap">
            <p class="hb-eyebrow">ESPACE REVENDEUR</p>
            <h1>Un espace réservé<br>aux professionnels</h1>
            <p class="hb-lead">Accédez au catalogue avec les tarifs professionnels, préparez votre liste de commande et suivez vos demandes. L’accès est ouvert après validation de votre compte par notre équipe.</p>
            <div class="hb-actions">
                @auth
                    <a class="hb-button hb-button-gold" href="{{ route('dashboard') }}">Mon espace <x-icon name="arrow-right" :size="15" /></a>
                @else
                    <a class="hb-button hb-button-gold" href="{{ route('register') }}"><x-icon name="user-plus" :size="15" /> Créer mon compte</a>
                    <a class="hb-button hb-button-outline" href="{{ route('login') }}"><x-icon name="log-in" :size="15" /> Se connecter</a>
                @endauth
            </div>
        </div>
    </section>

    <section class="hb-block">
        <div class="hb-wrap">
            <p class="hb-eyebrow">COMMENT ÇA MARCHE</p>
            <h2>Trois étapes</h2>
            <div class="hb-grid hb-grid-3">
                <div class="hb-card reveal">
                    <span class="hb-card-icon"><x-icon name="user-plus" :size="22" :stroke="1.5" /></span>
                    <h3>1. Créez votre compte</h3>
                    <p>Renseignez votre nom, votre société, votre activité et votre téléphone.</p>
                </div>
                <div class="hb-card reveal" style="--d:.07s">
                    <span class="hb-card-icon"><x-icon name="badge-check" :size="22" :stroke="1.5" /></span>
                    <h3>2. Validation</h3>
                    <p>Notre équipe vérifie votre demande et active votre compte.</p>
                </div>
                <div class="hb-card reveal" style="--d:.14s">
                    <span class="hb-card-icon"><x-icon name="package" :size="22" :stroke="1.5" /></span>
                    <h3>3. Catalogue et commandes</h3>
                    <p>Consultez les tarifs, composez votre liste et envoyez votre commande.</p>
                </div>
            </div>
            <p class="hb-muted hb-narrow" style="margin-top:28px">
                Vous pouvez parcourir le catalogue sans compte : <a class="hb-link" href="{{ route('catalog.index') }}">voir le catalogue</a>. Les prix sont réservés aux revendeurs approuvés.
            </p>
        </div>
    </section>

    @include('partials.hb-cta', ['heading' => 'Une question avant de vous inscrire ?', 'subject' => 'reseller', 'text' => 'Contactez-nous, nous vous répondons rapidement.'])
</x-hb-layout>
