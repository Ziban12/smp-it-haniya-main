<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstHeaderSetting extends Model
{
    protected $table = 'mst_header_settings';
    protected $primaryKey = 'header_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'title',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];
}
