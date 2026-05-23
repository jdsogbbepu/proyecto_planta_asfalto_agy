<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingreso extends Model
{
    protected $table = 'ingresos';

    protected $fillable = [
        'nro_ticket',
        'odc',
        'id_proveedor',
        'id_usuario',
        'id_funcionario',
        'fecha_adquirida',
        'observaciones',
    ];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'id_funcionario');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleIngreso::class, 'id_ingreso');
    }
}
