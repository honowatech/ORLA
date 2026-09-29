<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $table = 'activity';
    public $timestamps = true;
    protected $visible = array('action', 'jour', 'heure', 'id_user','color', 'texte_lien','lien');

    public function user()
    {
        return $this->belongsTo('App\Models\users\Users', 'id_user');
    }
}