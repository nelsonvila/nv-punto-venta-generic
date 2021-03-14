<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table = 'cajas';
    protected $fillable = [
        'id',
        'fecha',
        'monto_inicio',
        'monto_final',
        'idusuario',
        'estado'
    ];
}
