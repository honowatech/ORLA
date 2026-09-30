<?php

namespace App\Models\Point_relais;

use App\Models\Commandes\Commandes;
use App\Models\Coursiers\Coursiers;
use App\Models\Quartier\Quartier;
use App\Models\Stock\Stock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Point_relais extends Model
{
    protected $table = 'point_relais';

    public $timestamps = true;

    protected $fillable = ['id_quartier'];

    protected $visible = ['libelle', 'id_coursier', 'quartier', 'statut'];

    public function quartier(): BelongsTo
    {
        return $this->belongsTo(Quartier::class, 'id_quartier');
    }

    public function coursier(): BelongsTo
    {
        return $this->belongsTo(Coursiers::class, 'id_coursier');
    }

    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class, 'id_point_relais');
    }

    public function commandes(): HasMany
    {
        return $this->hasMany(Commandes::class, 'id_point_relais');
    }
}
