<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected $fillable = [
        'nombre',
        'ubicacion',
        'encargado',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    public function detallesIngreso(): HasMany
    {
        return $this->hasMany(DetalleIngreso::class, 'id_proyecto');
    }

    public function salidas(): HasMany
    {
        return $this->hasMany(Salida::class, 'id_proyecto');
    }
}
