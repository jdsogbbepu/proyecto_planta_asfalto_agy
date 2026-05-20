<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Funcionario extends Model
{
    protected $table = 'funcionarios';

    protected $fillable = [
        'nombre',
        'cargo',
        'area',
        'activo',
    ];

    public function salidas(): HasMany
    {
        return $this->hasMany(Salida::class, 'id_funcionario');
    }
}
