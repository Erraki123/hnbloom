<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'company', 'subject', 'message', 'status'];

    public const SUBJECTS = [
        'quote' => 'Demande de devis',
        'reseller' => 'Devenir revendeur',
        'product' => 'Renseignement sur un produit',
        'order' => 'Suivi de commande',
        'support' => 'Support technique / SAV',
        'claim' => 'Réclamation',
        'partner' => 'Partenariat',
        'other' => 'Autre',
    ];

    public const STATUSES = [
        'new' => 'Nouveau',
        'read' => 'Lu',
        'replied' => 'Répondu',
        'archived' => 'Archivé',
    ];

    public function getSubjectLabelAttribute(): string
    {
        return self::SUBJECTS[$this->subject] ?? $this->subject;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
