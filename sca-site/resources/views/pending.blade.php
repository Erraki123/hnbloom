<x-guest-layout>
    <div class="text-center">
        <h2 class="text-xl font-bold mb-2">Compte en attente de validation</h2>
        <p class="text-gray-600 mb-4">
            Votre inscription a bien été reçue. Un administrateur doit valider
            votre compte avant que vous puissiez accéder à votre espace.
        </p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-sm underline">Se déconnecter</button>
        </form>
    </div>
</x-guest-layout>
