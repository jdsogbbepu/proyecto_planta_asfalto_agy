<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetalleIngreso extends Model
{
    protected $table = 'detalle_ingresos';

    protected $fillable = [
        'id_ingreso',
        'id_material',
        'id_proyecto',
        'cantidad_adquirida',
        'cantidad_actual_lote',
    ];

    public function ingreso(): BelongsTo
    {
        return $this->belongsTo(Ingreso::class, 'id_ingreso');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'id_material');
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function detallesSalida(): HasMany
    {
        return $this->hasMany(DetalleSalida::class, 'id_detalle_ingreso');
    }
}
