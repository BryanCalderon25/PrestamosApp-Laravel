<?php

namespace App\Iteradores;

class IteradorPrestamos implements \Iterator
{
    private array $prestamos;
    private int $posicion = 0;

    public function __construct(array $prestamos)
    {
        $this->prestamos = $prestamos;
    }

    public function current()
    {
        return $this->prestamos[$this->posicion];
    }

    public function key()
    {
        return $this->posicion;
    }

    public function next()
    {
        $this->posicion++;
    }

    public function rewind()
    {
        $this->posicion = 0;
    }

    public function valid()
    {
        return isset($this->prestamos[$this->posicion]);
    }
}
