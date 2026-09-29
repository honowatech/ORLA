<?php

namespace App\Models\TypeClient;

use Illuminate\Database\Eloquent\Model;

class TypeClient extends Model 
{

    protected $table = 'type_client';
    public $timestamps = true;
    protected $visible = array('libelle');

}