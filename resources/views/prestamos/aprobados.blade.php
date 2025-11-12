@php
    use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('content')
<h3 class="mb-4">Historial de Solicitudes</h3>

<ul class="nav nav-tabs mb-4" id="prestamosTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="prestamos-tab" data-bs-toggle="tab" data-bs-target="#prestamos-panel" type="button" role="tab" aria-controls="prestamos-panel" aria-selected="true">
            Listado y proceso
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about-panel" type="button" role="tab" aria-controls="about-panel" aria-selected="false">
            Sobre el iterador
        </button>
    </li>
</ul>

<div class="tab-content" id="prestamosTabsContent">
    {{-- PANEL DE LISTADO DE PRÉSTAMOS --}}
    <div class="tab-pane fade show active" id="prestamos-panel" role="tabpanel" aria-labelledby="prestamos-tab">
        @if ($totalPrestamos > 0)
            <div class="row g-4 align-items-start">
                {{-- COLUMNA IZQUIERDA --}}
                <div class="col-lg-6">
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
                </div>

                {{-- COLUMNA DERECHA - BITÁCORA --}}
                <div class="col-lg-6">
                    @php
                        $bitacora = $coleccion->obtenerBitacoraIteracion();
                        $finalizacion = $coleccion->obtenerEventosFinalizacion();
                    @endphp

                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h5 class="card-title">Bitácora del iterador</h5>
                            <p class="card-subtitle text-muted mb-3">
                                Aquí se muestran todos los métodos ejecutados durante la iteración de cada préstamo.
                            </p>

                            {{-- BITÁCORA DE EVENTOS --}}
                            @if(empty($bitacora))
                                <div class="alert alert-secondary">No se registraron eventos de iteración.</div>
                            @else
                                <div class="accordion" id="bitacoraIterador">
                                    @foreach($bitacora as $indice => $registro)
                                        @php
                                            $idUnico = Str::slug($registro['prestamo']->nombre_cliente . '-' . $indice);
                                        @endphp
                                        <div class="accordion-item border mb-3">
                                            <h2 class="accordion-header bg-light px-3 py-2">
                                                <strong>Préstamo {{ $indice + 1 }}:</strong> {{ $registro['prestamo']->nombre_cliente }}
                                            </h2>
                                            <div id="collapse-{{ $idUnico }}" class="accordion-collapse collapse show">
                                                <div class="accordion-body">
                                                    <ol class="ps-3 mb-0">
                                                        @foreach($registro['eventos'] as $evento)
                                                            <li class="mb-2">
                                                                <strong>{{ $evento['metodo'] }}</strong>
                                                                <div class="text-muted small">{{ $evento['mensaje'] }}</div>
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- EVENTOS DE FINALIZACIÓN --}}
                            @if (!empty($finalizacion))
                                <div class="alert alert-light border mt-3">
                                    @foreach($finalizacion as $evento)
                                        <div><strong>{{ $evento['metodo'] }}</strong> — {{ $evento['mensaje'] }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">No hay préstamos registrados todavía.</div>
        @endif

        <div class="text-center mt-4">
            <a href="{{ route('prestamos.crear') }}" class="btn btn-outline-secondary">Volver al formulario</a>
        </div>
    </div>

    {{-- PANEL SOBRE EL ITERADOR --}}
    <div class="tab-pane fade" id="about-panel" role="tabpanel" aria-labelledby="about-tab">
        @if(isset($pasosIterador))
            <section class="mt-4">
                <h4 class="mb-3 text-center">¿Cómo recorre la colección nuestro iterador?</h4>
                <p class="text-muted text-center mb-4">
                    Cada bloque representa un método de <code>IteradorPrestamos</code> mostrando cómo PHP recorre los préstamos dentro del <em>foreach</em>.
                </p>
                <div class="d-flex flex-wrap justify-content-center align-items-stretch gap-3">
                    @foreach($pasosIterador as $paso)
                        <div class="card shadow-sm border-0" style="min-width: 200px; max-width: 240px;">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-primary text-uppercase">Paso {{ $loop->iteration }}</span>
                                    <span class="fs-4">{{ $paso['titulo'] }}</span>
                                </div>
                                <p class="fw-semibold text-dark mb-2">{{ $paso['resumen'] }}</p>
                                <p class="text-muted small mb-0">{{ $paso['detalle'] }}</p>
                            </div>
                        </div>
                        @if(! $loop->last)
                            <div class="d-flex align-items-center justify-content-center flex-column text-primary">
                                <span class="display-6">&#10132;</span>
                                <span class="small text-muted">Siguiente</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
@endsection
