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
    // Muestra el formulario
    public function crear()
    {
        return view('prestamos.crear');
    }

    // Procesa un préstamo aplicando Chain of Responsibility
    public function procesar(Request $request)
    {
        // Crear el préstamo con los datos del formulario
        $prestamo = new Prestamo();
        $prestamo->nombre_cliente = $request->nombre_cliente;
        $prestamo->monto_solicitado = $request->monto_solicitado;
        $prestamo->historial_crediticio = $request->historial_crediticio;
        $prestamo->ingresos_mensuales = $request->ingresos_mensuales;

        // Construcción de la cadena de validadores
        $v1 = new ValidadorIdentidad();     // Primer paso
        $v2 = new ValidadorHistorial();     // Segundo paso
        $v3 = new ValidadorCapacidadPago(); // Tercer paso

        // Enlazar los validadores en cadena
        $v1->establecerSiguiente($v2)->establecerSiguiente($v3);

        // Ejecutar la cadena
        if ($v1->manejar($prestamo)) {
            // Si todas las validaciones pasan
            $prestamo->estado = 'Aprobado';
            $prestamo->motivo_rechazo = null;
            $prestamo->save();

            session()->flash('mensaje', '✅ Préstamo aprobado con éxito.');
            session()->flash('tipo', 'success');
        } else {
            // Si la cadena falla en algún punto
            $prestamo->estado = 'Rechazado';
            $prestamo->motivo_rechazo = $v1->obtenerMotivoRechazo();
            $prestamo->save();

            session()->flash('mensaje', '❌ Préstamo rechazado: ' . $prestamo->motivo_rechazo);
            session()->flash('tipo', 'danger');
        }

        return redirect()->route('prestamos.crear');
    }

    // Página de historial con iterador
    public function aprobados()
    {
        // Obtener préstamos en orden cronológico (el más viejo primero)
        $prestamos = Prestamo::oldest()->get();

        // Crear colección personalizada para aplicar Iterador
        $coleccion = new ColeccionPrestamos();
        $totalPrestamos = $prestamos->count();

        // Agregar cada préstamo a la colección
        foreach ($prestamos as $prestamo) {
            $coleccion->agregarPrestamo($prestamo);
        }

        // Datos para explicar los métodos del iterador en la vista
        $pasosIterador = [
            [
                'titulo' => 'rewind()',
                'resumen' => 'Coloca el puntero en el inicio.',
                'detalle' => 'Se ejecuta automáticamente antes del primer ciclo del foreach.'
            ],
            [
                'titulo' => 'current()',
                'resumen' => 'Devuelve el elemento actual.',
                'detalle' => 'Entrega el préstamo que se mostrará en pantalla.'
            ],
            [
                'titulo' => 'key()',
                'resumen' => 'Devuelve el índice actual.',
                'detalle' => 'Sirve para saber en qué posición va el foreach.'
            ],
            [
                'titulo' => 'next()',
                'resumen' => 'Avanza al siguiente elemento.',
                'detalle' => 'Se dispara al terminar cada iteración del foreach.'
            ],
            [
                'titulo' => 'valid()',
                'resumen' => 'Comprueba si hay más elementos.',
                'detalle' => 'Cuando devuelve false, el foreach termina.'
            ],
        ];

        return view('prestamos.aprobados', compact('coleccion', 'pasosIterador', 'totalPrestamos'));
    }
}
