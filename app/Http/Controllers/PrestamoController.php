<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Handlers\{
    ValidadorIdentidad,
    ValidadorHistorial,
    ValidadorCapacidadPago
};
use App\Iteradores\ColeccionPrestamos;

class PrestamoController extends Controller
{
    public function crear()
    {
        return view('prestamos.crear');
    }

    public function procesar(Request $request)
    {
        $prestamo = new Prestamo();
        $prestamo->nombre_cliente = $request->nombre_cliente;
        $prestamo->monto_solicitado = $request->monto_solicitado;
        $prestamo->historial_crediticio = $request->historial_crediticio;
        $prestamo->ingresos_mensuales = $request->ingresos_mensuales;

        // Crear cadena de responsabilidad
        $v1 = new ValidadorIdentidad();
        $v2 = new ValidadorHistorial();
        $v3 = new ValidadorCapacidadPago();

        $v1->establecerSiguiente($v2)->establecerSiguiente($v3);

        // Procesar la solicitud
        if ($v1->manejar($prestamo)) {
            $prestamo->estado = 'Aprobado';
            $prestamo->motivo_rechazo = null;
            $prestamo->save();

            session()->flash('mensaje', '✅ Préstamo aprobado con éxito.');
            session()->flash('tipo', 'success');
        } else {
            $prestamo->estado = 'Rechazado';
            $motivo = $v1->obtenerMotivoRechazo();
            $prestamo->motivo_rechazo = $motivo; // se guarda el motivo
            $prestamo->save();

            session()->flash('mensaje', '❌ Préstamo rechazado: ' . $motivo);
            session()->flash('tipo', 'danger');
        }

        return redirect()->route('prestamos.crear');
    }

    public function aprobados()
    {
        $prestamos = Prestamo::latest()->get();
        $coleccion = new ColeccionPrestamos();
        $totalPrestamos = $prestamos->count();

        foreach ($prestamos as $prestamo) {
            $coleccion->agregarPrestamo($prestamo);
        }

        $pasosIterador = [
            [
                'titulo' => 'rewind()',
                'resumen' => 'Posiciona el puntero al inicio de la colección antes de comenzar.',
                'detalle' => 'Se ejecuta automáticamente cuando la vista inicia el foreach, preparando el primer elemento.'
            ],
            [
                'titulo' => 'current()',
                'resumen' => 'Obtiene el préstamo que corresponde a la posición actual.',
                'detalle' => 'Entrega el modelo de préstamo que se renderiza en la tarjeta de la lista.'
            ],
            [
                'titulo' => 'key()',
                'resumen' => 'Devuelve la clave asociada al elemento actual.',
                'detalle' => 'Sirve para que PHP sepa en qué índice vamos mientras se itera.'
            ],
            [
                'titulo' => 'next()',
                'resumen' => 'Avanza el puntero al siguiente préstamo disponible.',
                'detalle' => 'Se dispara al terminar cada vuelta del foreach para preparar el siguiente ciclo.'
            ],
            [
                'titulo' => 'valid()',
                'resumen' => 'Comprueba si aún hay elementos que recorrer.',
                'detalle' => 'Cuando ya no hay más préstamos, devuelve false y el bucle concluye.'
            ],
        ];

        return view('prestamos.aprobados', compact('coleccion', 'pasosIterador', 'totalPrestamos'));
    }
}
