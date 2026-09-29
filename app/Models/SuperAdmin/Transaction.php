<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    protected $table = 'super_admin_transaction';
    public $timestamps = true;

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = array('id_client','id_abonnement','methode','montant','nbre_abonnement','date_debut','date_fin');
    protected $visible = array('id_client','id_abonnement','methode','montant','nbre_abonnement','date_debut','date_fin');

    public function client()
    {
        return $this->belongsTo('App\Models\SuperAdmin\Client', 'id_client');
    }
    public function abonnement()
    {
        return $this->belongsTo('App\Models\SuperAdmin\Abonnement', 'id_abonnement');
    }
    public function infos()
    {
        return $this->hasMany('App\Models\SuperAdmin\Info_transaction', 'id_transaction');
    }
}