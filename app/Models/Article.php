<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'content', 'author_id'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
