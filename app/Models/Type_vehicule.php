<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type_vehicule extends Model
{
    use HasFactory;
    protected $table = 'type_vehicule';
    public $timestamps = true;
    protected $visible = array('libelle','description','statut','vitesse_moyenne','cout_km','cout_heure','heure_debut_autorisee','heure_fin_autorisee','capacite_colis');
    protected $fillable = array('libelle','description','statut','vitesse_moyenne','cout_km','cout_heure','heure_debut_autorisee','heure_fin_autorisee','capacite_colis');

    /** Plage horaire de circulation autorisée (null = pas de restriction). */
    public function plageHoraire(): ?array
    {
        if ($this->heure_debut_autorisee === null || $this->heure_fin_autorisee === null) {
            return null;
        }
        return [substr($this->heure_debut_autorisee, 0, 5), substr($this->heure_fin_autorisee, 0, 5)];
    }


    public function vehicule()
    {
        return $this->HasMany('App\Models\Vehicule', 'id_type');
    }
}
