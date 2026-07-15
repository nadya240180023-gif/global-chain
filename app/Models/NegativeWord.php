<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NegativeWord extends Model
{
    use HasFactory;

    protected $table = 'negative_words';

    protected $fillable = ['word'];
}
