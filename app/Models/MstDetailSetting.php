<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstDetailSetting extends Model
{
    protected $table = 'mst_detail_settings';
    protected $primaryKey = 'detail_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'header_id',
        'item_code',
        'item_name',
        'item_desc',
        'status',
        'item_type',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];
}
