<?php

namespace App\Models\Commandes;

use App\Models\Concerns\BelongsToEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commandes extends Model 
{
    use BelongsToEntreprise, HasFactory;

    protected $table = 'commandes';
    public $timestamps = true;
    protected $visible = array('id_client', 'nom_client', 'telephone', 'id_boutique','id_coursier', 'id_point_relais', 'id_saver', 'date_commande', 'type_commande', 'adresse_colis', 'id_quartier_colis', 'adresse_livraison', 'id_quartier_livraison', 'montant_livraison', 'id_montant_livraison', 'date_mise_encours', 'date_livre', 'date_livraison','montant_recuperer', 'description', 'mode_de_paiement', 'statut');

    public function client()
    {
        return $this->belongsTo('App\Models\Clients\Clients', 'id_client');
    }

    public function enregistreur()
    {
        return $this->belongsTo('App\Models\User', 'id_saver');
    }

    public function agent()
    {
        return $this->belongsTo('App\Models\Agents\Agents', 'id_agent');
    }

    public function boutique()
    {
        return $this->belongsTo('App\Models\Boutiques\Boutiques', 'id_boutique');
    }

    public function coursier()
    {
        return $this->belongsTo('App\Models\Coursiers\Coursiers', 'id_coursier');
    }

    public function point_relais()
    {
        return $this->belongsTo('App\Models\Point_relais\Point_relais', 'id_point_relais');
    }

    public function quartier_colis()
    {
        return $this->belongsTo('App\Models\Quartier\Quartier', 'id_quartier_colis');
    }

    public function quartier_livraison()
    {
        return $this->belongsTo('App\Models\Quartier\Quartier', 'id_quartier_livraison');
    }

    public function montant_livraison()
    {
        return $this->belongsTo('App\Models\Montant_livraison\Montant_livraison', 'id_montant_livraison');
    }

    public function details_commande()
    {
        return $this->hasMany('App\Models\Details_commande\Details_commande', 'id_commande');
    }

}