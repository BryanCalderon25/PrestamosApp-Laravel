<?php

namespace App\Handlers;

use App\Models\Prestamo;

class ValidadorCapacidadPago extends ValidadorBase
{
    // Revisa si los ingresos son menores al 10% del monto solicitado
    protected function procesar(Prestamo $prestamo): bool
    {
        if ($prestamo->ingresos_mensuales < $prestamo->monto_solicitado / 10) {
            $this->motivoRechazo = 'Los ingresos mensuales son insuficientes para cubrir el préstamo.';
            return false;
        }

        return true;
    }
}
