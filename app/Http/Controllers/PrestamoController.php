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

        foreach ($prestamos as $prestamo) {
            $coleccion->agregarPrestamo($prestamo);
        }

        return view('prestamos.aprobados', compact('coleccion'));
    }
}
