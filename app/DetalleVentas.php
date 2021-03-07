<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleVentas extends Model
{
    //
    protected $table = 'detalle_ventas';
    protected $fillable = [
        'idventa',
        'idproducto',
        'cantidad',
        'precio'
    ];

    public $timestamps = false;
}
