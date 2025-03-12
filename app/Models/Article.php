<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'source_id',
        'author_id',
        'title',
        'description',
        'content',
        'url',
        'url_to_image',
        'published_at',
        'provider',
        'type',
        'additional_data',
    ];
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'article_category');
    }
}
