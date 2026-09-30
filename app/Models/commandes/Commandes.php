<?php

namespace App\Models\Commandes;

use Illuminate\Database\Eloquent\Model;

class Commandes extends Model
{
    protected $table = 'commandes';

    public $timestamps = true;

    protected $visible = ['id_client', 'nom_client', 'telephone', 'id_boutique', 'id_coursier', 'id_point_relais', 'id_saver', 'date_commande', 'type_commande', 'adresse_colis', 'id_quartier_colis', 'adresse_livraison', 'id_quartier_livraison', 'montant_livraison', 'id_montant_livraison', 'date_mise_encours', 'date_livre', 'date_livraison', 'montant_recuperer', 'description', 'mode_de_paiement', 'statut'];

    public function client()
    {
        return $this->belongsTo('App\Models\clients\Clients', 'id_client');
    }

    public function enregistreur()
    {
        return $this->belongsTo('App\Models\users\Users', 'id_saver');
    }

    public function agent()
    {
        return $this->belongsTo('App\Models\agents\Agents', 'id_agent');
    }

    public function boutique()
    {
        return $this->belongsTo('App\Models\boutiques\Boutiques', 'id_boutique');
    }

    public function coursier()
    {
        return $this->belongsTo('App\Models\coursiers\Coursiers', 'id_coursier');
    }

    public function point_relais()
    {
        return $this->belongsTo('App\Models\point_relais\Point_relais', 'id_point_relais');
    }

    public function quartier_colis()
    {
        return $this->belongsTo('App\Models\quartier\Quartier', 'id_quartier_colis');
    }

    public function quartier_livraison()
    {
        return $this->belongsTo('App\Models\quartier\Quartier', 'id_quartier_livraison');
    }

    public function montant_livraison()
    {
        return $this->belongsTo('App\Models\montant_livraison\Montant_livraison', 'id_montant_livraison');
    }

    public function details_commande()
    {
        return $this->hasMany('App\Models\details_commande\Details_commande', 'id_commande');
    }
}
