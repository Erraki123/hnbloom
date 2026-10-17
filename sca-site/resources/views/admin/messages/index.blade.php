<x-admin-layout title="Messages">
    <div class="ad-page-head">
        <div>
            <p class="ad-eyebrow">Communication</p>
            <h2>Messages de contact</h2>
            <p class="ad-muted">Demandes reçues via le formulaire de contact du site.</p>
        </div>
    </div>

    <article class="ad-card">
        <form method="GET" class="ad-toolbar">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher (nom, e-mail, société, texte)…" class="ad-grow">
            <select name="status">
                <option value="">Tous les statuts</option>
                @foreach(\App\Models\ContactMessage::STATUSES as $k => $l)
                    <option value="{{ $k }}" @selected(request('status') == $k)>{{ $l }}</option>
                @endforeach
            </select>
            <select name="subject">
                <option value="">Tous les sujets</option>
                @foreach(\App\Models\ContactMessage::SUBJECTS as $k => $l)
                    <option value="{{ $k }}" @selected(request('subject') == $k)>{{ $l }}</option>
                @endforeach
            </select>
            <button type="submit" class="ad-btn ad-btn-gold"><x-ad-icon name="search" :size="16" /> Filtrer</button>
            @if(request()->hasAny(['q', 'status', 'subject']))
                <a href="{{ route('admin.messages.index') }}" class="ad-btn">Réinitialiser</a>
            @endif
        </form>

        @if($messages->isEmpty())
            <div class="ad-empty"><x-ad-icon name="mail" :size="28" /><p>Aucun message.</p></div>
        @else
            <div class="ad-table-wrap">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Expéditeur</th>
                            <th>Sujet</th>
                            <th>Aperçu</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $m)
                            <tr class="{{ $m->status === 'new' ? 'is-new' : '' }}">
                                <td class="ad-nowrap ad-muted">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong>{{ $m->name }}</strong>
                                    <small>{{ $m->company ?: $m->email }}</small>
                                </td>
                                <td>{{ $m->subject_label }}</td>
                                <td class="ad-preview">{{ \Illuminate\Support\Str::limit($m->message, 60) }}</td>
                                <td><span class="ad-pill s-{{ $m->status }}">{{ $m->status_label }}</span></td>
                                <td>
                                    <div class="ad-row-actions">
                                        <a href="{{ route('admin.messages.show', $m) }}" class="ad-icon-btn" title="Ouvrir" aria-label="Ouvrir"><x-ad-icon name="eye" :size="17" /></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </article>

    <div class="ad-pager">{{ $messages->links() }}</div>
</x-admin-layout>
