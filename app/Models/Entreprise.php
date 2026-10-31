<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Entreprise cliente de la plateforme (tenant).
 *
 * Anciennement « super_admin_client » : chaque entreprise a ses propres
 * utilisateurs, livreurs, véhicules, zones, commandes… et ne voit jamais
 * celles des autres.
 */
class Entreprise extends Model
{
    use SoftDeletes;

    protected $table = 'entreprises';

    public $timestamps = true;

    protected $fillable = [
        'name', 'slug', 'email', 'adresse', 'telephone', 'telephone_secondaire', 'cni',
        'logo', 'couleur_principale', 'devise', 'fuseau_horaire',
        'id_abonnement', 'date_fin', 'date_dernier_paiement', 'essai_jusqu_au',
        'id_proprietaire', 'statut',
    ];

    protected $casts = [
        'date_fin' => 'datetime',
        'date_dernier_paiement' => 'datetime',
        'essai_jusqu_au' => 'datetime',
        'statut' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Entreprise $entreprise) {
            if (empty($entreprise->slug)) {
                $entreprise->slug = static::slugDisponible($entreprise->name ?: 'entreprise');
            }
        });
    }

    public static function slugDisponible(string $nom): string
    {
        $base = Str::slug($nom) ?: 'entreprise';
        $slug = $base;
        for ($i = 2; static::withTrashed()->where('slug', $slug)->exists(); $i++) {
            $slug = $base.'-'.$i;
        }

        return $slug;
    }

    public function abonnement()
    {
        return $this->belongsTo('App\Models\SuperAdmin\Abonnement', 'id_abonnement');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\SuperAdmin\Transaction', 'id_client');
    }

    public function utilisateurs()
    {
        return $this->hasMany(User::class, 'entreprise_id');
    }

    public function proprietaire()
    {
        return $this->belongsTo(User::class, 'id_proprietaire');
    }

    public function estActive(): bool
    {
        return (bool) $this->statut;
    }

    public function enEssai(): bool
    {
        return $this->essai_jusqu_au !== null && $this->essai_jusqu_au->isFuture();
    }

    /**
     * Fin effective de l'accès payé, période de grâce comprise.
     */
    public function finAcces(): ?Carbon
    {
        if ($this->date_fin === null) {
            return null;
        }

        return $this->date_fin->copy()->addDays((int) ($this->abonnement->periode_grace ?? 0));
    }

    public function abonnementValide(): bool
    {
        $fin = $this->finAcces();

        return $this->id_abonnement !== null && $fin !== null && ! $fin->isPast();
    }

    /**
     * L'entreprise peut-elle travailler normalement (essai ou abonnement à jour) ?
     */
    public function accesComplet(): bool
    {
        return $this->estActive() && ($this->enEssai() || $this->abonnementValide());
    }
}
