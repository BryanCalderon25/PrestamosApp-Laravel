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

    // validación Chain of Responsibility
    public function procesar(Request $request)
    {
        // ValidatesRequests
        $this->validate($request, [
            'nombre_cliente'      => 'required|string|max:100',
            'monto_solicitado'    => 'required|numeric|min:1|max:999999999',
            'historial_crediticio'=> 'required|numeric|min:300|max:850',
            'ingresos_mensuales'  => 'required|numeric|min:0',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'numeric'  => 'El campo :attribute debe ser un número.',
            'min'      => 'El valor del campo :attribute es demasiado bajo.',
            'max'      => 'El valor del campo :attribute excede el límite permitido.',
        ]);

        // Crear el préstamo con los datos del formulario
        $prestamo = new Prestamo();
        $prestamo->nombre_cliente = $request->nombre_cliente;
        $prestamo->monto_solicitado = $request->monto_solicitado;
        $prestamo->historial_crediticio = $request->historial_crediticio;
        $prestamo->ingresos_mensuales = $request->ingresos_mensuales;

        // Construcción de la cadena de validadores
        $v1 = new ValidadorIdentidad();     // Primero
        $v2 = new ValidadorHistorial();     // Segundo
        $v3 = new ValidadorCapacidadPago(); // Tercero

        // Enlazar los validadores
        $v1->establecerSiguiente($v2)->establecerSiguiente($v3);

     // Ejecutar la cadena de validación
        if ($v1->manejar($prestamo)) {
            // Si todos los validadores permiten continuar
            $prestamo->estado = 'Aprobado';
            $prestamo->motivo_rechazo = null;
        } else {
            // Si algún validador falla, se detiene y se obtiene el motivo
            $prestamo->estado = 'Rechazado';
            $prestamo->motivo_rechazo = $v1->obtenerMotivoRechazo();
        }

        // Guardar en la base de datos
        $prestamo->save();

        // Mensajes para la vista
        session()->flash(
            'mensaje',
            ($prestamo->estado === 'Aprobado')
                ? '✅ Préstamo aprobado con éxito.'
                : '❌ Préstamo rechazado: ' . $prestamo->motivo_rechazo
        );

        session()->flash('tipo', $prestamo->estado === 'Aprobado' ? 'success' : 'danger');

        return redirect()->route('prestamos.crear');
    }

    // Página de historial con iterador
    public function aprobados()
    {
        $prestamos = Prestamo::oldest()->get();

        $coleccion = new ColeccionPrestamos();
        $totalPrestamos = $prestamos->count();

        foreach ($prestamos as $prestamo) {
            $coleccion->agregarPrestamo($prestamo);
        }

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
                'detalle' => 'Indica en qué posición está la iteración.'
            ],
            [
                'titulo' => 'next()',
                'resumen' => 'Avanza al siguiente elemento.',
                'detalle' => 'Se dispara después de cada iteración del foreach.'
            ],
            [
                'titulo' => 'valid()',
                'resumen' => 'Verifica si quedan elementos.',
                'detalle' => 'Cuando devuelve false, el foreach se detiene.'
            ],
        ];

        return view('prestamos.aprobados', compact('coleccion', 'pasosIterador', 'totalPrestamos'));
    }
}
