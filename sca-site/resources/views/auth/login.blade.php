<x-guest-layout>
    <p class="lg-kicker">CONNEXION SÉCURISÉE</p>
    <h1>Connexion</h1>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="lg-field">
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                          placeholder="vous@societe.com" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="lg-field">
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                          placeholder="••••••••" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="lg-row">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember">
                <span class="ms-2 text-sm">Se souvenir de moi</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            @endif
        </div>

        <button type="submit" class="lg-submit">Se connecter →</button>
    </form>

    <div class="lg-foot">
        <span>Pas encore de compte ?</span>
        @if (Route::has('register'))
            <a href="{{ route('register') }}">Créer un compte</a>
        @else
            <a href="{{ route('contact') }}">Nous contacter</a>
        @endif
    </div>
</x-guest-layout>
