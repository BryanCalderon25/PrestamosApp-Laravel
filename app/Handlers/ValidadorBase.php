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
        // Si este validador falla, detenemos la cadena y guardamos el motivo
        if (!$this->procesar($prestamo)) {
            return false;
        }

        // Si pasa, pero hay otro validador después, lo ejecutamos
        if ($this->siguiente) {
            $resultado = $this->siguiente->manejar($prestamo);

            // Si el siguiente falla, tomamos su motivo
            if (!$resultado) {
                $this->motivoRechazo = $this->siguiente->obtenerMotivoRechazo();
            }

            return $resultado;
        }

        // Si no hay más validadores, todo fue exitoso
        return true;
    }

    public function obtenerMotivoRechazo(): ?string
    {
        return $this->motivoRechazo;
    }

    abstract protected function procesar(Prestamo $prestamo): bool;
}
