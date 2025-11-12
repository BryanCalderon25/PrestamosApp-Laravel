<?php

namespace App\Iteradores;

use App\Models\Prestamo;

class ColeccionPrestamos implements \IteratorAggregate
{
    private array $prestamos = [];

    public function agregarPrestamo(Prestamo $prestamo)
    {
        $this->prestamos[] = $prestamo;
    }

    public function getIterator(): \Traversable
    {
        return new IteradorPrestamos($this->prestamos);
    }
}
