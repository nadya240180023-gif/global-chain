<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'email',
        'telepon',
        'alamat',
        'status',
    ];

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
