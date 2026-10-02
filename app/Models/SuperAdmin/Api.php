<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Api extends Model
{
    protected $table = 'super_admin_api';

    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'key', 'user', 'password', 'secret', 'statut'];

    protected $visible = ['name', 'key', 'user', 'password', 'secret', 'statut'];

    /**
     * Identifiants de passerelle chiffrés en base (la rotation d'APP_KEY les rendrait illisibles).
     */
    protected $casts = [
        'key' => 'encrypted',
        'secret' => 'encrypted',
        'password' => 'encrypted',
    ];
}
