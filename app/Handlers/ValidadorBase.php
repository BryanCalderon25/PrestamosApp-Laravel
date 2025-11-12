<?php

namespace App\Handlers;

use App\Models\Prestamo;

abstract class ValidadorBase
{
    // Siguiente validador en la cadena
    protected ?ValidadorBase $siguiente = null;

    // Motivo del rechazo si este validador falla
    protected ?string $motivoRechazo = null;

    // Enlaza otro validador después de este
    public function establecerSiguiente(ValidadorBase $validador): ValidadorBase
    {
        $this->siguiente = $validador;
        return $validador;
    }

    // Ejecuta este validador y continúa la cadena si pasa
    public function manejar(Prestamo $prestamo): bool
    {
        // Si este validador falla, se detiene la cadena
        if (!$this->procesar($prestamo)) {
            return false;
        }

        // Si hay siguiente validador, lo ejecutamos
        if ($this->siguiente) {
            $resultado = $this->siguiente->manejar($prestamo);

            // Tomar motivo del rechazo del siguiente
            if (!$resultado) {
                $this->motivoRechazo = $this->siguiente->obtenerMotivoRechazo();
            }

            return $resultado;
        }

        // Si no hay más validadores, todo pasó bien
        return true;
    }

    // Devuelve el motivo del rechazo
    public function obtenerMotivoRechazo(): ?string
    {
        return $this->motivoRechazo;
    }

    // Método que cada validador concreto debe implementar
    abstract protected function procesar(Prestamo $prestamo): bool;
}
