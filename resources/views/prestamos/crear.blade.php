@extends('layouts.app')

@section('content')
<h3 class="mb-4">Solicitud de Préstamo</h3>
<form method="POST" action="{{ route('prestamos.procesar') }}">
    @csrf
    <div class="mb-3">
        <label>Nombre del cliente</label>
        <input type="text" name="nombre_cliente" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Monto solicitado (₡)</label>
        <input type="number" name="monto_solicitado" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Historial crediticio (300 - 850)</label>
        <input type="number" name="historial_crediticio" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Ingresos mensuales (₡)</label>
        <input type="number" name="ingresos_mensuales" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Enviar solicitud</button>
</form>
@endsection
