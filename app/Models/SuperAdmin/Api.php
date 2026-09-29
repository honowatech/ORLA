<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Api extends Model
{
    protected $table = 'super_admin_api';
    public $timestamps = true;

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = array('name','key','user','password','secret','statut');
    protected $visible = array('name','key','user','password','secret','statut');
}