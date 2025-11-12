<?php

namespace App\Iteradores;

use App\Models\Prestamo;

class ColeccionPrestamos implements \IteratorAggregate
{
    private array $prestamos = [];
    private array $bitacora = [];
    private array $finalizacion = [];

    public function agregarPrestamo(Prestamo $prestamo)
    {
        $this->prestamos[] = $prestamo;
        $indice = count($this->prestamos) - 1;

        $this->bitacora[$indice] = [
            'prestamo' => $prestamo,
            'eventos' => [[
                'tipo' => 'insercion',
                'metodo' => 'agregarPrestamo()',
                'mensaje' => "Se inserta el préstamo en la posición {$indice} de la colección personalizada.",
            ]],
        ];
    }

    public function getIterator(): \Traversable
    {
        $this->prepararBitacoraParaIteracion();

        $registrador = function (string $metodo, int $posicion, $resultado = null) {
            if ($metodo === 'valid' && $resultado === false) {
                $this->finalizacion[] = [
                    'metodo' => 'valid()',
                    'mensaje' => 'valid() devuelve false y el foreach termina porque ya no hay más préstamos.',
                ];

                return;
            }

            if (! isset($this->bitacora[$posicion])) {
                return;
            }

            $this->bitacora[$posicion]['eventos'][] = [
                'tipo' => 'iteracion',
                'metodo' => $metodo . '()',
                'mensaje' => $this->describirEvento($metodo, $posicion, $resultado),
            ];
        };

        return new IteradorPrestamos($this->prestamos, $registrador);
    }

    private function prepararBitacoraParaIteracion(): void
    {
        foreach ($this->bitacora as &$registro) {
            $registro['eventos'] = array_values(array_filter(
                $registro['eventos'],
                fn (array $evento) => ($evento['tipo'] ?? 'iteracion') === 'insercion'
            ));
        }
        unset($registro);

        $this->finalizacion = [];
    }

    private function describirEvento(string $metodo, int $posicion, $resultado): string
    {
        $prestamoActual = $this->prestamos[$posicion] ?? null;

        return match ($metodo) {
            'rewind' => $prestamoActual
                ? "El puntero vuelve al inicio para arrancar con el préstamo de {$prestamoActual->nombre_cliente}."
                : 'El puntero vuelve al inicio, pero la colección está vacía.',
            'valid' => $resultado
                ? "valid() confirma que existe un préstamo en la posición {$posicion}."
                : "valid() indica que la posición {$posicion} ya no tiene un préstamo disponible.",
            'current' => $prestamoActual
                ? "current() entrega el préstamo de {$prestamoActual->nombre_cliente} para renderizarlo."
                : 'current() intenta acceder, pero no hay un préstamo disponible en la posición actual.',
            'key' => "key() informa que se está procesando la posición {$posicion}.",
            'next' => $resultado instanceof Prestamo
                ? "next() avanza el puntero hacia el préstamo de {$resultado->nombre_cliente}."
                : 'next() avanza el puntero y deja la colección lista para finalizar.',
            default => '',
        };
    }

    public function obtenerBitacoraIteracion(): array
    {
        return $this->bitacora;
    }

    public function obtenerEventosFinalizacion(): array
    {
        return $this->finalizacion;
    }

    public function contar(): int
    {
        return count($this->prestamos);
    }
}
