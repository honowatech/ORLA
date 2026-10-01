<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abonnement extends Model
{
    protected $table = 'super_admin_abonnement';
    public $timestamps = true;

    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = array('titre','accumulateur','type_periode','montant','periode_grace','statut');
    protected $visible = array('titre','accumulateur','type_periode','montant','periode_grace','statut');

    public function transactions()
    {
        return $this->hasMany('App\Models\SuperAdmin\Transaction', 'id_abonnement');
    }
    public function client()
    {
        return $this->hasMany('App\Models\SuperAdmin\Client', 'id_abonnement');
    }
}