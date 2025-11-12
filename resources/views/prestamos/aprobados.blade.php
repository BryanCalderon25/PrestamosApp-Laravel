@extends('layouts.app')

@section('content')
<h3 class="mb-4">Préstamos Aprobados</h3>
@if (iterator_count($coleccion->getIterator()) > 0)
    <ul class="list-group">
        @foreach($coleccion as $prestamo)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>{{ $prestamo->nombre_cliente }}</strong>
                <span>₡{{ number_format($prestamo->monto_solicitado, 2) }}</span>
            </li>
        @endforeach
    </ul>
@else
    <div class="alert alert-info">No hay préstamos aprobados aún.</div>
@endif
@endsection
