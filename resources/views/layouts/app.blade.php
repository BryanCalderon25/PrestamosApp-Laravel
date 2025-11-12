<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('prestamos.crear') }}">Banco UCR</a>
            <a class="nav-link text-white" href="{{ route('prestamos.aprobados') }}">Ver Aprobados</a>
        </div>
    </nav>
    <div class="container mt-5">
        @yield('content')
    </div>
</body>
</html>
