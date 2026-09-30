<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Model;

class Info_transaction extends Model
{
    protected $table = 'super_admin_info_transaction';

    public $timestamps = true;

    protected $dates = ['deleted_at'];

    protected $fillable = ['id_transaction', 'name', 'value'];

    protected $visible = ['id_transaction', 'name', 'value'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'id_transaction');
    }
}
