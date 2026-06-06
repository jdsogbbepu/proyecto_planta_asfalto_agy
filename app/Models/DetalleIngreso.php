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
        'nro_registro',
        'id_material',
        'id_proyecto',
        'id_proveedor',
        'fecha_lote',
        'cantidad_adquirida',
        'cantidad_actual_lote',
        'acciones_planificadas',
    ];

    protected $casts = [
        'fecha_lote' => 'date',
        'cantidad_adquirida' => 'decimal:2',
        'cantidad_actual_lote' => 'decimal:2',
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

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function detallesSalida(): HasMany
    {
        return $this->hasMany(DetalleSalida::class, 'id_detalle_ingreso');
    }

    public function getCantidadUtilizadaAttribute(): float
    {
        return (float) $this->detallesSalida()->sum('cantidad_salida');
    }

    public function getStockPlantaAttribute(): float
    {
        return (float) $this->cantidad_adquirida - $this->cantidad_utilizada;
    }

    public function getEstadoLoteAttribute(): string
    {
        if ($this->cantidad_actual_lote <= 0) {
            return 'AGOTADO';
        } elseif ($this->cantidad_actual_lote < $this->cantidad_adquirida) {
            return 'PARCIAL';
        }
        return 'COMPLETO';
    }

    public static function generarNroRegistro(int $anio): string
    {
        $lockKey = 'nro_registro_lock_' . $anio;

        return \Illuminate\Support\Facades\Cache::lock($lockKey, 10)->block(5, function () use ($anio) {
            $ultimo = static::where('nro_registro', 'like', 'RGTR-' . $anio . '-%')
                ->orderBy('id', 'desc')
                ->first();

            $sigienteNumero = $ultimo ? ((int) str_replace('RGTR-' . $anio . '-', '', $ultimo->nro_registro)) + 1 : 1;

            return 'RGTR-' . $anio . '-' . str_pad($sigienteNumero, 4, '0', STR_PAD_LEFT);
        });
    }
}
