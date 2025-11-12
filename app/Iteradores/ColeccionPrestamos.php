<?php

namespace App\Iteradores;

use App\Models\Prestamo;

class ColeccionPrestamos implements \IteratorAggregate
{
    private array $prestamos = [];   // Préstamos almacenados
    private array $bitacora = [];    // Eventos por préstamo
    private array $finalizacion = []; // Eventos de cierre del iterador

    // Agrega un préstamo a la colección
    public function agregarPrestamo(Prestamo $prestamo)
    {
        $this->prestamos[] = $prestamo;            // Se agrega al final
        $indice = count($this->prestamos) - 1;     // Posición asignada

        // Registrar inserción en bitácora
        $this->bitacora[$indice] = [
            'prestamo' => $prestamo,
            'eventos' => [[
                'tipo' => 'insercion',
                'metodo' => 'agregarPrestamo()',
                'mensaje' => "Se inserta en la posición {$indice}.",
            ]],
        ];
    }

    // Devuelve el iterador personalizado
    public function getIterator(): \Traversable
    {
        $this->prepararBitacoraParaIteracion(); // Limpia para nueva ejecución

        // Closure para registrar métodos del iterador
        $registrador = function (string $metodo, int $posicion, $resultado = null) {

            // Registrar finalización del foreach
            if ($metodo === 'valid' && $resultado === false) {
                $this->finalizacion[] = [
                    'metodo' => 'valid()',
                    'mensaje' => 'valid() devuelve false y el foreach termina.',
                ];
                return;
            }

            if (!isset($this->bitacora[$posicion])) {
                return; // Si no hay registro, no hacemos nada
            }

            // Guardar evento del método ejecutado
            $this->bitacora[$posicion]['eventos'][] = [
                'tipo' => 'iteracion',
                'metodo' => $metodo . '()',
                'mensaje' => $this->describirEvento($metodo, $posicion, $resultado),
            ];
        };

        return new IteradorPrestamos($this->prestamos, $registrador);
    }

    // Limpia la bitácora, conserva solo inserciones
    private function prepararBitacoraParaIteracion(): void
    {
        foreach ($this->bitacora as &$registro) {
            $registro['eventos'] = array_values(array_filter(
                $registro['eventos'],
                fn(array $evento) => $evento['tipo'] === 'insercion'
            ));
        }
        unset($registro);

        $this->finalizacion = []; // Reinicia eventos de cierre
    }

    // Mensajes según el método del iterador
    private function describirEvento(string $metodo, int $posicion, $resultado): string
    {
        $prestamo = $this->prestamos[$posicion] ?? null;

        return match ($metodo) {
            'rewind' =>
                $prestamo ? "Puntero al inicio: {$prestamo->nombre_cliente}."
                          : 'Puntero al inicio (colección vacía).',

            'valid' =>
                $resultado ? "Hay préstamo en posición {$posicion}."
                           : "No hay préstamo en posición {$posicion}.",

            'current' =>
                $prestamo ? "Entrega préstamo de {$prestamo->nombre_cliente}."
                          : 'No hay préstamo actual.',

            'key' =>
                "Índice actual: {$posicion}.",

            'next' =>
                $resultado instanceof Prestamo
                    ? "Avanza hacia {$resultado->nombre_cliente}."
                    : 'Avanza y deja lista la finalización.',

            default => '',
        };
    }

    // Devuelve la bitácora completa
    public function obtenerBitacoraIteracion(): array
    {
        return $this->bitacora;
    }

    // Devuelve eventos del final del foreach
    public function obtenerEventosFinalizacion(): array
    {
        return $this->finalizacion;
    }

    // Cantidad de préstamos
    public function contar(): int
    {
        return count($this->prestamos);
    }
}
