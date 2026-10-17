<x-admin-layout title="Messages">
    <a href="{{ route('admin.messages.index') }}" class="ad-back"><x-ad-icon name="arrow-left" :size="15" /> Retour aux messages</a>

    <div class="ad-page-head">
        <div>
            <p class="ad-eyebrow">Message reçu le {{ $message->created_at->format('d/m/Y à H:i') }}</p>
            <h2>{{ $message->subject_label }}</h2>
        </div>
        <span class="ad-pill s-{{ $message->status }}">{{ $message->status_label }}</span>
    </div>

    <section class="ad-detail">
        <article class="ad-card">
            <header class="ad-card-head"><div><h3>Expéditeur</h3></div></header>
            <dl class="ad-dl">
                <div><dt>Nom</dt><dd>{{ $message->name }}</dd></div>
                @if($message->company)<div><dt>Société</dt><dd>{{ $message->company }}</dd></div>@endif
                <div><dt>E-mail</dt><dd><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd></div>
                @if($message->phone)<div><dt>Téléphone</dt><dd><a href="tel:{{ $message->phone }}">{{ $message->phone }}</a></dd></div>@endif
            </dl>
        </article>

        <article class="ad-card">
            <header class="ad-card-head"><div><h3>Statut</h3><p class="ad-muted">Suivi du traitement</p></div></header>
            <form method="POST" action="{{ route('admin.messages.status', $message) }}" class="ad-status-form">
                @csrf @method('PATCH')
                <select name="status">
                    @foreach(\App\Models\ContactMessage::STATUSES as $k => $l)
                        <option value="{{ $k }}" @selected($message->status === $k)>{{ $l }}</option>
                    @endforeach
                </select>
                <button type="submit" class="ad-btn ad-btn-gold"><x-ad-icon name="check" :size="16" /> Mettre à jour</button>
            </form>
        </article>
    </section>

    <article class="ad-card">
        <header class="ad-card-head"><div><h3>Message</h3></div></header>
        <div class="ad-message-body">{{ $message->message }}</div>
    </article>

    <div class="ad-actions ad-actions-end">
        <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->subject_label) }}" class="ad-btn ad-btn-gold"><x-ad-icon name="reply" :size="16" /> Répondre par e-mail</a>

        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Supprimer ce message ? Cette action est définitive.">
            @csrf @method('DELETE')
            <button type="submit" class="ad-btn ad-btn-outline-danger"><x-ad-icon name="trash-2" :size="16" /> Supprimer</button>
        </form>
    </div>
</x-admin-layout>
