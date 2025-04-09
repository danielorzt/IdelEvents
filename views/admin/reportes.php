<?php
session_start();
// Evitar que el navegador cachee esta página
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verificación de sesión y rol
if (!isset($_SESSION['id_usuario'])) {
    header("Location: /IdealEventsx/views/login.php");
    exit();
}

// Solo permitir acceso a administradores
if ($_SESSION['rol'] !== 'admin') {
    header("Location: /IdealEventsx/views/cliente/dashboard.php");
    exit();
}

// Incluir modelos necesarios
include_once "../../models/EventoModel.php";
include_once "../../models/UsuarioModel.php";
include_once "../../models/InscripcionModel.php";
include_once "../../models/PagoModel.php";

// Obtener estadísticas básicas
$total_eventos = count(EventoModel::mdlListarEventos());
$total_usuarios = count(UsuarioModel::mdlListarUsuarios());

// Obtener datos para los gráficos
$eventos_por_categoria = [];
$categorias = EventoModel::mdlObtenerCategorias();
foreach ($categorias as $categoria) {
    $eventos_por_categoria[] = [
        'categoria' => $categoria,
        'cantidad' => count(EventoModel::mdlBuscarEventos('', $categoria))
    ];
}

// Datos para el gráfico de usuarios por mes
$fecha_actual = new DateTime();
$usuarios_por_mes = [];
for ($i = 5; $i >= 0; $i--) {
    $fecha = clone $fecha_actual;
    $fecha->modify("-$i month");
    $mes = $fecha->format('Y-m');
    $mes_nombre = $fecha->format('M Y');

    // Contar usuarios registrados en ese mes
    $usuarios_mes = 0;
    $todos_usuarios = UsuarioModel::mdlListarUsuarios();
    foreach ($todos_usuarios as $usuario) {
        $fecha_registro = new DateTime($usuario['fecha_registro']);
        if ($fecha_registro->format('Y-m') === $mes) {
            $usuarios_mes++;
        }
    }

    $usuarios_por_mes[] = [
        'mes' => $mes_nombre,
        'cantidad' => $usuarios_mes
    ];
}

// Estadísticas de pagos
$total_pagos = 0;
$pagos_completados = 0;
$pagos_pendientes = 0;
$ingresos_total = 0;

// En un sistema real, esto vendría de una consulta a la base de datos
// Aquí usamos datos de ejemplo
$total_pagos = 85;
$pagos_completados = 65;
$pagos_pendientes = 20;
$ingresos_total = 3500.50;

// Preparar datos de pagos por mes
$pagos_por_mes = [
    ['mes' => 'Ene', 'monto' => 450.25],
    ['mes' => 'Feb', 'monto' => 620.75],
    ['mes' => 'Mar', 'monto' => 380.50],
    ['mes' => 'Abr', 'monto' => 510.30],
    ['mes' => 'May', 'monto' => 720.80],
    ['mes' => 'Jun', 'monto' => 820.90]
];

// Preparar datos de eventos por mes
$eventos_por_mes = EventoModel::mdlObtenerEstadisticasPorMes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Ideal Event's</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/main.css">
    <!-- Añadimos Chart.js para los gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .navbar-logo {
            height: 40px !important;
            width: auto !important;
            object-fit: contain !important;
        }
        .stat-card {
            border-radius: 10px;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .admin-bg-primary {
            background-color: #4e73df;
            color: white;
        }
        .admin-bg-success {
            background-color: #1cc88a;
            color: white;
        }
        .admin-bg-warning {
            background-color: #f6c23e;
            color: white;
        }
        .admin-bg-danger {
            background-color: #e74a3b;
            color: white;
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <img src="../../public/logo/logo.png" alt="Logo" class="navbar-logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="eventos.php"><i class="bi bi-calendar-event me-1"></i> Eventos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="usuarios.php"><i class="bi bi-people me-1"></i> Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="reportes.php"><i class="bi bi-graph-up me-1"></i> Reportes</a>
                </li>
            </ul>
            <div class="dropdown ms-auto">
                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['nombre'] ?? 'Admin') ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/IdealEventsx/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-graph-up me-2"></i>Reportes y Estadísticas</h2>
        <div>
            <button type="button" class="btn btn-primary" id="btnDescargarReporte">
                <i class="bi bi-file-earmark-pdf me-1"></i> Descargar Reporte Completo
            </button>
            <button type="button" class="btn btn-outline-secondary" id="btnFiltrarReportes">
                <i class="bi bi-funnel me-1"></i> Filtrar
            </button>
            <div class="btn-group ms-2">
                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-calendar3 me-1"></i> Periodo
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item active" href="#">Último mes</a></li>
                    <li><a class="dropdown-item" href="#">Trimestre actual</a></li>
                    <li><a class="dropdown-item" href="#">Este año</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">Personalizado...</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card admin-bg-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-uppercase mb-1">
                                Eventos Totales</div>
                            <div class="h5 mb-0 fw-bold"><?= $total_eventos ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card admin-bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-uppercase mb-1">
                                Usuarios Registrados</div>
                            <div class="h5 mb-0 fw-bold"><?= $total_usuarios ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card admin-bg-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-uppercase mb-1">
                                Ingresos Totales</div>
                            <div class="h5 mb-0 fw-bold">$<?= number_format($ingresos_total, 2) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card admin-bg-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-uppercase mb-1">
                                Pagos Pendientes</div>
                            <div class="h5 mb-0 fw-bold"><?= $pagos_pendientes ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <!-- Gráfico de Eventos por Categoría -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 fw-bold"><i class="bi bi-pie-chart me-2"></i>Eventos por Categoría</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="eventosPorCategoriaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Usuarios por Mes -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 fw-bold"><i class="bi bi-people me-2"></i>Registro de Usuarios por Mes</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="usuariosPorMesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Ingresos por Mes -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-warning text-dark">
                    <h6 class="m-0 fw-bold"><i class="bi bi-currency-dollar me-2"></i>Ingresos por Mes</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="ingresosPorMesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Eventos por Mes -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 fw-bold"><i class="bi bi-calendar-event me-2"></i>Eventos por Mes</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="eventosPorMesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablas de Estadísticas -->
    <div class="row">
        <!-- Top Eventos más Populares -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 fw-bold"><i class="bi bi-star me-2"></i>Eventos Más Populares</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tablaEventosPopulares" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Evento</th>
                                <th>Categoría</th>
                                <th>Fecha</th>
                                <th>Inscritos</th>
                                <th>Ingresos</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Concierto de Rock</td>
                                <td>Concierto</td>
                                <td>15/11/2023</td>
                                <td>145</td>
                                <td>$7,250.00</td>
                            </tr>
                            <tr>
                                <td>Feria Tecnológica</td>
                                <td>Tecnología</td>
                                <td>20/11/2023</td>
                                <td>98</td>
                                <td>$1,470.00</td>
                            </tr>
                            <tr>
                                <td>Festival Gastronómico</td>
                                <td>Gastronomía</td>
                                <td>05/12/2023</td>
                                <td>76</td>
                                <td>$2,660.00</td>
                            </tr>
                            <tr>
                                <td>Concierto Sinfónico</td>
                                <td>Concierto</td>
                                <td>10/01/2024</td>
                                <td>65</td>
                                <td>$3,900.00</td>
                            </tr>
                            <tr>
                                <td>Taller de Cocina Italiana</td>
                                <td>Gastronomía</td>
                                <td>01/02/2024</td>
                                <td>54</td>
                                <td>$2,700.00</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos Pagos -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 fw-bold"><i class="bi bi-credit-card me-2"></i>Últimos Pagos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tablaUltimosPagos" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Evento</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>#12345</td>
                                <td>Juan Pérez</td>
                                <td>Concierto de Pop</td>
                                <td>08/04/2024</td>
                                <td>$65.00</td>
                                <td><span class="badge bg-success">Completado</span></td>
                            </tr>
                            <tr>
                                <td>#12344</td>
                                <td>María Gómez</td>
                                <td>Taller de Programación</td>
                                <td>07/04/2024</td>
                                <td>$40.00</td>
                                <td><span class="badge bg-success">Completado</span></td>
                            </tr>
                            <tr>
                                <td>#12343</td>
                                <td>Carlos López</td>
                                <td>Feria de Empleo</td>
                                <td>06/04/2024</td>
                                <td>$0.00</td>
                                <td><span class="badge bg-success">Completado</span></td>
                            </tr>
                            <tr>
                                <td>#12342</td>
                                <td>Ana Rodríguez</td>
                                <td>Exhibición de Danza</td>
                                <td>05/04/2024</td>
                                <td>$35.00</td>
                                <td><span class="badge bg-warning text-dark">Pendiente</span></td>
                            </tr>
                            <tr>
                                <td>#12341</td>
                                <td>Pedro Sánchez</td>
                                <td>Conferencia de Sostenibilidad</td>
                                <td>04/04/2024</td>
                                <td>$15.00</td>
                                <td><span class="badge bg-danger">Rechazado</span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribución de Usuarios -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-dark text-white">
                    <h6 class="m-0 fw-bold"><i class="bi bi-bar-chart me-2"></i>Distribución de Usuarios por Roles y Actividad</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-container">
                                <canvas id="usuariosPorRolChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-container">
                                <canvas id="usuariosActividadChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Filtrar Reportes -->
<div class="modal fade" id="modalFiltrarReportes" tabindex="-1" aria-labelledby="modalFiltrarReportesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalFiltrarReportesLabel"><i class="bi bi-funnel me-2"></i>Filtrar Reportes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formFiltrarReportes">
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                    </div>
                    <div class="mb-3">
                        <label for="categoria_filtro" class="form-label">Categoría</label>
                        <select class="form-select" id="categoria_filtro" name="categoria">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= htmlspecialchars($categoria) ?>"><?= htmlspecialchars($categoria) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="estado_pago" class="form-label">Estado de Pago</label>
                        <select class="form-select" id="estado_pago" name="estado_pago">
                            <option value="">Todos</option>
                            <option value="completado">Completados</option>
                            <option value="pendiente">Pendientes</option>
                            <option value="rechazado">Rechazados</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnAplicarFiltros">
                    <i class="bi bi-funnel-fill me-1"></i> Aplicar Filtros
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0">© <?= date('Y') ?> Ideal Event's. Todos los derechos reservados.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-white me-3">Términos y Condiciones</a>
                <a href="#" class="text-white">Política de Privacidad</a>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        // Inicializar DataTables
        $('#tablaEventosPopulares').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            paging: false,
            searching: false,
            info: false
        });

        // Gráfico de Ingresos por Mes
        const ctxIngresosPorMes = document.getElementById('ingresosPorMesChart').getContext('2d');
        const ingresosPorMesChart = new Chart(ctxIngresosPorMes, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($pagos_por_mes, 'mes')) ?>,
                datasets: [{
                    label: 'Ingresos ($)',
                    data: <?= json_encode(array_column($pagos_por_mes, 'monto')) ?>,
                    backgroundColor: 'rgba(246, 194, 62, 0.2)',
                    borderColor: 'rgba(246, 194, 62, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '

                                $('#tablaUltimosPagos').DataTable({
                                    language: {
                                        url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                                    },
                                    paging: false,
                                    searching: false,
                                    info: false
                                });

                                // Modal de filtrado
                                $('#btnFiltrarReportes').click(function() {
                                    $('#modalFiltrarReportes').modal('show');
                                });

                                // Botón descargar reporte
                                $('#btnDescargarReporte').click(function() {
                                    alert('Descargando reporte completo...');
                                    // En un sistema real, aquí se haría la petición al servidor
                                });

                                // Aplicar filtros
                                $('#btnAplicarFiltros').click(function() {
                                    $('#modalFiltrarReportes').modal('hide');
                                    alert('Filtros aplicados. Los reportes se actualizarán.');
                                    // En un sistema real, aquí se haría la petición al servidor con los filtros
                                });

                                // Gráfico de Eventos por Categoría
                                const ctxEventosPorCategoria = document.getElementById('eventosPorCategoriaChart').getContext('2d');
                                const eventosPorCategoriaChart = new Chart(ctxEventosPorCategoria, {
                                    type: 'pie',
                                    data: {
                                        labels: <?= json_encode(array_column($eventos_por_categoria, 'categoria')) ?>,
                                        datasets: [{
                                            data: <?= json_encode(array_column($eventos_por_categoria, 'cantidad')) ?>,
                                            backgroundColor: [
                                                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69', '#858796',
                                                '#6f42c1', '#20c9a6', '#f7c59f', '#6f7de0'
                                            ],
                                            hoverOffset: 4
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                position: 'bottom'
                                            },
                                            title: {
                                                display: true,
                                                text: 'Distribución de Eventos por Categoría'
                                            }
                                        }
                                    }
                                });

                                // Gráfico de Usuarios por Mes
                                const ctxUsuariosPorMes = document.getElementById('usuariosPorMesChart').getContext('2d');
                                const usuariosPorMesChart = new Chart(ctxUsuariosPorMes, {
                                    type: 'bar',
                                    data: {
                                        labels: <?= json_encode(array_column($usuarios_por_mes, 'mes')) ?>,
                                        datasets: [{
                                            label: 'Nuevos Usuarios',
                                            data: <?= json_encode(array_column($usuarios_por_mes, 'cantidad')) ?>,
                                            backgroundColor: 'rgba(28, 200, 138, 0.7)',
                                            borderColor: 'rgba(28, 200, 138, 1)',
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        },
                                        plugins: {
                                            title: {
                                                display: true,
                                                text: 'Registro de Usuarios por Mes'
                                            }
                                        }
                                    }
                                }); + value;
                            }
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Ingresos Mensuales'
                    }
                }
            }
        });

        // Gráfico de Eventos por Mes
        const ctxEventosPorMes = document.getElementById('eventosPorMesChart').getContext('2d');
        const eventosPorMesChart = new Chart(ctxEventosPorMes, {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                datasets: [{
                    label: 'Eventos Creados',
                    data: <?= json_encode(array_column($eventos_por_mes, 'total')) ?>,
                    backgroundColor: 'rgba(54, 185, 204, 0.7)',
                    borderColor: 'rgba(54, 185, 204, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Eventos Creados por Mes'
                    }
                }
            }
        });

        // Gráfico de Usuarios por Rol
        const ctxUsuariosPorRol = document.getElementById('usuariosPorRolChart').getContext('2d');
        const usuariosPorRolChart = new Chart(ctxUsuariosPorRol, {
            type: 'doughnut',
            data: {
                labels: ['Administradores', 'Clientes'],
                datasets: [{
                    data: [4, <?= $total_usuarios - 4 ?>],
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Distribución de Usuarios por Rol'
                    }
                }
            }
        });

        // Gráfico de Actividad de Usuarios
        const ctxUsuariosActividad = document.getElementById('usuariosActividadChart').getContext('2d');
        const usuariosActividadChart = new Chart(ctxUsuariosActividad, {
            type: 'bar',
            data: {
                labels: ['Activos (Último mes)', 'Inactivos (>1 mes)', 'Nuevos (Última semana)'],
                datasets: [{
                    label: 'Usuarios',
                    data: [45, <?= $total_usuarios - 45 - 12 ?>, 12],
                    backgroundColor: [
                        'rgba(28, 200, 138, 0.7)',
                        'rgba(231, 74, 59, 0.7)',
                        'rgba(54, 185, 204, 0.7)'
                    ],
                    borderColor: [
                        'rgba(28, 200, 138, 1)',
                        'rgba(231, 74, 59, 1)',
                        'rgba(54, 185, 204, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Actividad de Usuarios'
                    }
                }
            }
        });
    });
</script>
</body>
</html>