<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstArticle extends Model
{
    protected $table = 'mst_articles';
    protected $primaryKey = 'article_id';
    public $incrementing = false;
    protected $keyType = 'stirng';
    public $timestamps = true;

    protected $fillable = [
        'article_id',
        'title',
        'slug',
        'content',
        'image',
        'article_type',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];
}
