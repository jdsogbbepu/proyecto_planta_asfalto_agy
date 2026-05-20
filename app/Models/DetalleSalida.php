<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleSalida extends Model
{
    protected $table = 'detalle_salidas';

    protected $fillable = [
        'id_salida',
        'id_detalle_ingreso',
        'cantidad_salida',
    ];

    public function salida(): BelongsTo
    {
        return $this->belongsTo(Salida::class, 'id_salida');
    }

    public function detalleIngreso(): BelongsTo
    {
        return $this->belongsTo(DetalleIngreso::class, 'id_detalle_ingreso');
    }
}
