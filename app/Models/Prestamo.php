<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $table = 'prestamos';

    protected $fillable = [
        'nombre_cliente',
        'monto_solicitado',
        'historial_crediticio',
        'ingresos_mensuales',
        'estado'
    ];
}
