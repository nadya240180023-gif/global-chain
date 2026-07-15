<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsCache extends Model
{
    use HasFactory;

    protected $table = 'news_cache';

    protected $fillable = [
        'country_id',
        'title',
        'description',
        'content',
        'url',
        'source',
        'published_at',
        'category',
        'sentiment',
        'sentiment_score',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
