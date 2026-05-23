<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salida extends Model
{
    protected $table = 'salidas';

    protected $fillable = [
        'id_funcionario',
        'id_proyecto',
        'id_usuario',
        'odc',
        'uso',
        'fecha_salida',
    ];

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'id_funcionario');
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleSalida::class, 'id_salida');
    }
}
