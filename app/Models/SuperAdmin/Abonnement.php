<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abonnement extends Model
{
    protected $table = 'super_admin_abonnement';

    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['titre', 'accumulateur', 'type_periode', 'salaire', 'date_modif'];

    protected $visible = ['titre', 'accumulateur', 'type_periode', 'salaire', 'date_modif'];

    public function transactions()
    {
        return $this->hasMany('App\Models\SuperAdmin\Transaction', 'id_abonnement');
    }

    public function client()
    {
        return $this->hasMany('App\Models\SuperAdmin\Client', 'id_abonnement');
    }
}
