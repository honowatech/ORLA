<?php

namespace App\Models\SuperAdmin;

use App\Models\Entreprise;

/**
 * Vue « Super Admin » d'une entreprise cliente. Conservée pour les contrôleurs
 * et vues existants : la table est désormais « entreprises ».
 */
class Client extends Entreprise
{
    protected $visible = array('name','adresse','telephone','id_abonnement','telephone_secondaire','date_fin','date_dernier_paiement','statut');
}
