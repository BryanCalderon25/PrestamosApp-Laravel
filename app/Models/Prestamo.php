<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'prestamos';

    // Campos que se pueden llenar 
    protected $fillable = [
        'nombre_cliente',         // Nombre del solicitante
        'monto_solicitado',       // Monto que pide el cliente
        'historial_crediticio',   // Puntaje crediticio 
        'ingresos_mensuales',     // Ingresos 
        'estado',                 // Aprobado / Rechazado
        'motivo_rechazo'          // Motivo cuando algún validador falla
    ];
}
