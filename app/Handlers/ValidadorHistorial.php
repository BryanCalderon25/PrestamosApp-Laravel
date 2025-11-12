<?php

namespace App\Handlers;

use App\Models\Prestamo;

class ValidadorHistorial extends ValidadorBase
{
    protected function procesar(Prestamo $prestamo): bool
    {
        return $prestamo->historial_crediticio >= 700;
    }
}
