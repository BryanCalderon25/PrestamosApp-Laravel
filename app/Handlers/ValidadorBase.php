<?php

namespace App\Handlers;

use App\Models\Prestamo;

abstract class ValidadorBase
{
    protected ?ValidadorBase $siguiente = null;

    public function establecerSiguiente(ValidadorBase $validador): ValidadorBase
    {
        $this->siguiente = $validador;
        return $validador;
    }

    public function manejar(Prestamo $prestamo): bool
    {
        if ($this->procesar($prestamo)) {
            if ($this->siguiente) {
                return $this->siguiente->manejar($prestamo);
            }
            return true;
        }
        return false;
    }

    abstract protected function procesar(Prestamo $prestamo): bool;
}
