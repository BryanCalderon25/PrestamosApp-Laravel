<?php

namespace App\Iteradores;

class IteradorPrestamos implements \Iterator
{
    private array $prestamos;
    private int $posicion = 0;
    /** @var callable|null */
    private $registrador;

    public function __construct(array $prestamos, ?callable $registrador = null)
    {
        $this->prestamos = $prestamos;
        $this->registrador = $registrador;
    }

    private function registrar(string $metodo, int $posicion, $resultado = null): void
    {
        if ($this->registrador) {
            ($this->registrador)($metodo, $posicion, $resultado);
        }
    }

    public function current()
    {
        $valor = $this->prestamos[$this->posicion];
        $this->registrar('current', $this->posicion, $valor);

        return $valor;
    }

    public function key()
    {
        $this->registrar('key', $this->posicion, $this->posicion);

        return $this->posicion;
    }

    public function next()
    {
        $posicionAnterior = $this->posicion;
        $this->posicion++;
        $siguiente = $this->prestamos[$this->posicion] ?? null;

        $this->registrar('next', $posicionAnterior, $siguiente);
    }

    public function rewind()
    {
        $this->posicion = 0;
        $inicio = $this->prestamos[$this->posicion] ?? null;

        $this->registrar('rewind', $this->posicion, $inicio);
    }

    public function valid()
    {
        $esValido = isset($this->prestamos[$this->posicion]);
        $this->registrar('valid', $this->posicion, $esValido);

        return $esValido;
    }
}
