<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BitacoraActividad extends Model
{
    protected $table = 'bitacora_actividades';

    protected $fillable = [
        'id_usuario',
        'accion',
        'detalle',
        'ip_address',
    ];

    /**
     * Obtiene el usuario asociado a la acción.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
