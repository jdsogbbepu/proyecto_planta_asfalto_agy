<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    protected $table = 'materials';

    protected $fillable = [
        'nombre',
        'descripcion',
        'id_medida',
        'stock_minimo',
    ];

    public function medida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'id_medida');
    }

    public function detallesIngreso(): HasMany
    {
        return $this->hasMany(DetalleIngreso::class, 'id_material');
    }
}
