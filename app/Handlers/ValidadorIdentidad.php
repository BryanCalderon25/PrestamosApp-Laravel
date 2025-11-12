<?php

namespace App\Handlers;

use App\Models\Prestamo;

class ValidadorIdentidad extends ValidadorBase
{
    protected function procesar(Prestamo $prestamo): bool
    {
        if (empty($prestamo->nombre_cliente)) {
            $this->motivoRechazo = 'Debe ingresar el nombre del cliente.';
            return false;
        }
        return true;
    }
}
