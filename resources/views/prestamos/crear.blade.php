@extends('layouts.app')

@section('content')
<h3 class="mb-4">Solicitud de Préstamo</h3>

{{-- Mostrar alerta --}}
@if (session('mensaje'))
    <div class="alert alert-{{ session('tipo') }} alert-dismissible fade show" role="alert">
        {{ session('mensaje') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

<form method="POST" action="{{ route('prestamos.procesar') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Nombre del cliente</label>
        <input type="text" name="nombre_cliente" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Monto solicitado (₡)</label>
        <input type="number" name="monto_solicitado" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Historial crediticio (300 - 850)</label>
        <input type="number" name="historial_crediticio" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Ingresos mensuales (₡)</label>
        <input type="number" name="ingresos_mensuales" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success w-100 mt-3">Enviar solicitud</button>
</form>

{{-- Botón para ir al historial --}}
<div class="text-center mt-4">
    <a href="{{ route('prestamos.aprobados') }}" class="btn btn-outline-primary">Ver historial de solicitudes</a>
</div>
@endsection
