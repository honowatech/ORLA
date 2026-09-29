<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    protected $table = 'super_admin_client';
    public $timestamps = true;

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = array('nom','adresse','telephone','id_abonnement','telephone_secondaire','date_debut_contrat','date_dernier_paiement','statut');
    protected $visible = array('nom','adresse','telephone','id_abonnement','telephone_secondaire','date_debut_contrat','date_dernier_paiement','statut');

    public function transactions()
    {
        return $this->hasMany('App\Models\SuperAdmin\Transaction', 'id_client');
    }
    public function abonnement()
    {
        return $this->belongsTo('App\Models\SuperAdmin\Abonnement', 'id_abonnement');
    }
}