<?php

namespace App\Iteradores;

class IteradorPrestamos implements \Iterator
{
    private array $prestamos;   // Arreglo de préstamos a recorrer
    private int $posicion = 0;  // Posición actual del puntero
    private $registrador;       // Callback para registrar eventos

    public function __construct(array $prestamos, ?callable $registrador = null)
    {
        $this->prestamos = $prestamos;     // Guarda la colección
        $this->registrador = $registrador; // Guarda el registrador
    }

    // Registra el método ejecutado y su resultado
    private function registrar(string $metodo, int $posicion, $resultado = null): void
    {
        if ($this->registrador) {
            ($this->registrador)($metodo, $posicion, $resultado);
        }
    }

    // Devuelve el elemento actual
    public function current()
    {
        $valor = $this->prestamos[$this->posicion];
        $this->registrar('current', $this->posicion, $valor);
        return $valor;
    }

    // Devuelve la posición actual
    public function key()
    {
        $this->registrar('key', $this->posicion, $this->posicion);
        return $this->posicion;
    }

    // Avanza una posición en la colección
    public function next()
    {
        $posicionAnterior = $this->posicion;
        $this->posicion++;
        $siguiente = $this->prestamos[$this->posicion] ?? null;

        $this->registrar('next', $posicionAnterior, $siguiente);
    }

    // Reinicia el puntero al inicio
    public function rewind()
    {
        $this->posicion = 0;
        $inicio = $this->prestamos[$this->posicion] ?? null;

        $this->registrar('rewind', $this->posicion, $inicio);
    }

    // Verifica si la posición actual tiene un elemento válido
    public function valid()
    {
        $esValido = isset($this->prestamos[$this->posicion]);
        $this->registrar('valid', $this->posicion, $esValido);
        return $esValido;
    }
}
