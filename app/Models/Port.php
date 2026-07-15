<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Port extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'country_id', 'latitude', 'longitude'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
