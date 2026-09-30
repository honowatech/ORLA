<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    protected $table = 'super_admin_transaction';

    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['id_client', 'id_abonnement', 'methode', 'montant', 'nbre_abonnement', 'date_debut', 'date_fin'];

    protected $visible = ['id_client', 'id_abonnement', 'methode', 'montant', 'nbre_abonnement', 'date_debut', 'date_fin'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    public function abonnement()
    {
        return $this->belongsTo(Abonnement::class, 'id_abonnement');
    }

    public function infos()
    {
        return $this->hasMany(Info_transaction::class, 'id_transaction');
    }
}
