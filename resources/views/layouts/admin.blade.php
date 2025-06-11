<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    @vite('resources/css/app.css')
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
    <nav class="bg-indigo-700 text-white shadow mb-8">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard') }}" class="font-bold text-2xl tracking-wide flex items-center">
                <i class="fas fa-cogs mr-2"></i> AdminPanel
            </a>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.vehiculos.index') }}" class="btn btn-secondary">Vehículos</a>
                <a href="{{ route('admin.reservas.index') }}" class="btn btn-secondary">Reservas</a>
                <a href="{{ route('admin.reservas.historial') }}" class="btn btn-secondary">Historial</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Usuarios</a>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Panel usuario</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-danger ml-2">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </nav>
    <main class="container mx-auto py-8">
        @yield('content')
    </main>
</body>
</html>