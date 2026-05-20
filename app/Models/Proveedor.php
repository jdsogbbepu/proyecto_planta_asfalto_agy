<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedors';

    protected $fillable = [
        'razon_social',
        'nit',
        'telefono',
        'direccion',
    ];

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class, 'id_proveedor');
    }
}
