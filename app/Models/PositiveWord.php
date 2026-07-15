<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PositiveWord extends Model
{
    use HasFactory;

    protected $table = 'positive_words';

    protected $fillable = ['word'];
}
