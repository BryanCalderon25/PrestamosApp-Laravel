<?php

namespace App\Handlers;

use App\Models\Prestamo;

class ValidadorCapacidadPago extends ValidadorBase
{
    protected function procesar(Prestamo $prestamo): bool
    {
        return $prestamo->ingresos_mensuales >= $prestamo->monto_solicitado / 10;
    }
}
