<?php

namespace App\Handlers;

use App\Models\Prestamo;

abstract class ValidadorBase
{
    protected ?ValidadorBase $siguiente = null;
    protected ?string $motivoRechazo = null;

    public function establecerSiguiente(ValidadorBase $validador): ValidadorBase
    {
        $this->siguiente = $validador;
        return $validador;
    }

    public function manejar(Prestamo $prestamo): bool
    {
        if ($this->procesar($prestamo)) {
            if ($this->siguiente) {
                $this->motivoRechazo = $this->siguiente->motivoRechazo;
                return $this->siguiente->manejar($prestamo);
            }
            return true;
        } else {
            // Si el validador falla, conserva su motivo
            return false;
        }
    }

    public function obtenerMotivoRechazo(): ?string
    {
        return $this->motivoRechazo;
    }

    abstract protected function procesar(Prestamo $prestamo): bool;
}
