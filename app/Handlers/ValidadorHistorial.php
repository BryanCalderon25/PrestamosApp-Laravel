<?php

namespace App\Handlers;

use App\Models\Prestamo;

class ValidadorHistorial extends ValidadorBase
{
    protected function procesar(Prestamo $prestamo): bool
    {
        if ($prestamo->historial_crediticio < 600) {
            $this->motivoRechazo = 'Historial crediticio insuficiente (mínimo 600).';
            return false;
        }
        return true;
    }
}
