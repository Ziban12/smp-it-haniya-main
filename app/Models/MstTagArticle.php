<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstTagArticle extends Model
{
    protected $table = 'mst_tag_articles';
    protected $primaryKey = 'tag_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'article_id',
        'tag_code',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];
}
