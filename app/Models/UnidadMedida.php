<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadMedida extends Model
{
    protected $table = 'unidad_medidas';

    protected $fillable = [
        'nombre',
        'abreviacion',
    ];

    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class, 'id_medida');
    }
}
