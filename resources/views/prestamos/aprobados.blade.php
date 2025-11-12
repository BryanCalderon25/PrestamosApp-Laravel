@extends('layouts.app')

@section('content')
<h3 class="mb-4">Historial de Solicitudes</h3>

@if (iterator_count($coleccion->getIterator()) > 0)
    <ul class="list-group shadow">
        @foreach($coleccion as $prestamo)
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                    <strong>{{ $prestamo->nombre_cliente }}</strong><br>
                    <small>Monto: ₡{{ number_format($prestamo->monto_solicitado, 2) }}</small><br>
                    <small>Historial: {{ $prestamo->historial_crediticio }}</small><br>
                    <small>Ingresos: ₡{{ number_format($prestamo->ingresos_mensuales, 2) }}</small><br>

                    @if ($prestamo->estado == 'Aprobado')
                        <span class="badge bg-success mt-2">Aprobado</span>
                    @else
                        <span class="badge bg-danger mt-2">Rechazado</span>
                        <div class="text-muted mt-1" style="font-size: 0.9em;">
                            Motivo: {{ $prestamo->motivo_rechazo }}
                        </div>
                    @endif
                </div>

                <span class="text-secondary">{{ $prestamo->created_at->format('d/m/Y H:i') }}</span>
            </li>
        @endforeach
    </ul>
@else
    <div class="alert alert-info">No hay préstamos registrados todavía.</div>
@endif

{{-- Botón para volver al formulario --}}
<div class="text-center mt-4">
    <a href="{{ route('prestamos.crear') }}" class="btn btn-outline-secondary">Volver al formulario</a>
</div>
@endsection
