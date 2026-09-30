<?php

namespace App\Models\Commandes;

use App\Models\Agents\Agents;
use App\Models\Boutiques\Boutiques;
use App\Models\Clients\Clients;
use App\Models\Coursiers\Coursiers;
use App\Models\Details_commande\Details_commande;
use App\Models\Montant_livraison\Montant_livraison;
use App\Models\Point_relais\Point_relais;
use App\Models\Quartier\Quartier;
use App\Models\Users\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commandes extends Model
{
    protected $table = 'commandes';

    protected $casts = [
        'date_commande' => 'datetime',
        'date_livraison' => 'datetime',
        'date_mise_encours' => 'datetime',
        'date_livre' => 'datetime',
    ];

    public $timestamps = true;

    public function client(): BelongsTo
    {
        return $this->belongsTo(Clients::class, 'id_client');
    }

    public function enregistreur(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'id_saver');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agents::class, 'id_agent');
    }

    public function boutique(): BelongsTo
    {
        return $this->belongsTo(Boutiques::class, 'id_boutique');
    }

    public function coursier(): BelongsTo
    {
        return $this->belongsTo(Coursiers::class, 'id_coursier');
    }

    public function point_relais(): BelongsTo
    {
        return $this->belongsTo(Point_relais::class, 'id_point_relais');
    }

    public function quartier_colis(): BelongsTo
    {
        return $this->belongsTo(Quartier::class, 'id_quartier_colis');
    }

    public function quartier_livraison(): BelongsTo
    {
        return $this->belongsTo(Quartier::class, 'id_quartier_livraison');
    }

    public function montant_livraison(): BelongsTo
    {
        return $this->belongsTo(Montant_livraison::class, 'id_montant_livraison');
    }

    public function details_commande(): HasMany
    {
        return $this->hasMany(Details_commande::class, 'id_commande');
    }
}
