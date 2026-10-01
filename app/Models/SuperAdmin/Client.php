<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    protected $table = 'super_admin_client';
    public $timestamps = true;

    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = array('name','adresse','telephone','cni','id_abonnement','telephone_secondaire','date_fin','date_dernier_paiement','statut');
    protected $visible = array('name','adresse','telephone','cni','id_abonnement','telephone_secondaire','date_fin','date_dernier_paiement','statut');

    public function transactions()
    {
        return $this->hasMany('App\Models\SuperAdmin\Transaction', 'id_client');
    }
    public function abonnement()
    {
        return $this->belongsTo('App\Models\SuperAdmin\Abonnement', 'id_abonnement');
    }
}