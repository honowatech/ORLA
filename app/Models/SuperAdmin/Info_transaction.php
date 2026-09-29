<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Info_transaction extends Model
{
    protected $table = 'super_admin_info_transaction';
    public $timestamps = true;

    protected $dates = ['deleted_at'];
    protected $fillable = array('id_transaction','name','value');
    protected $visible = array('id_transaction','name','value');
    
    public function transaction()
    {
        return $this->belongsTo('App\Models\SuperAdmin\Transaction', 'id_transaction');
    }
}