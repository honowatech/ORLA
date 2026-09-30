<?php

namespace App\Models;

use App\Models\Users\Users;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activity';

    public $timestamps = true;

    protected $visible = ['action', 'jour', 'heure', 'id_user', 'color', 'texte_lien', 'lien'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'id_user');
    }
}
