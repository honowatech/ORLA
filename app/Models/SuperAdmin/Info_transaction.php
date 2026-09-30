<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Info_transaction extends Model
{
    protected $table = 'super_admin_info_transaction';

    public $timestamps = true;

    protected $dates = ['deleted_at'];

    protected $fillable = ['id_transaction', 'name', 'value'];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'id_transaction');
    }
}
