<?php

namespace App\Handlers;

use App\Models\Prestamo;

class ValidadorIdentidad extends ValidadorBase
{
    protected function procesar(Prestamo $prestamo): bool
    {
        return !empty($prestamo->nombre_cliente);
    }
}
