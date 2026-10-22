<?php
// VIEW — dashboard.php
// Programación 4: Aplicaciones Web — UAE
// ===========================
require_once "../model/verificar_sesion.php";
require_once "../model/StockModel.php";

// Este módulo también se puede delegar (ej: un contador que solo
// necesita ver el stock, sin acceso a compras ni ventas)
if (!tienePermiso("dashboard")) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permiso+para+ver+este+módulo");
    exit();
}

$modelo       = new StockModel();
$masVendidos  = $modelo->obtenerMasVendidos();
$stockActual  = $modelo->obtenerStockActual();

// Convertimos el arreglo de PHP a un formato que JavaScript entienda.
$etiquetas = json_encode(array_column($masVendidos, "nombre"));
$datos     = json_encode(array_column($masVendidos, "total_vendido"));
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Stock de Productos — Mi Proyecto</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">

        <!-- NAVBAR -->
        <?php
        $rutaBase     = "../";
        $paginaActual = "dashboard.php";
        include "../partials/navbar.php";
        ?>

        <div id="layoutSidenav">
            <!-- SIDEBAR -->
            <?php include "../partials/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Stock de Productos</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Stock de Productos</li>
                        </ol>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-bar me-1"></i> Productos más vendidos
                            </div>
                            <div class="card-body">
                                <canvas id="graficoVendidos" height="90"></canvas>
                                <?php if (empty($masVendidos)): ?>
                                    <p class="text-muted mt-3">Aún no hay ventas registradas.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-boxes me-1"></i> Stock actual
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <a href="reporte_stock.php" class="btn btn-outline-primary" target="_blank">
                                        <i class="fas fa-file-pdf me-1"></i> Ver reporte PDF
                                    </a>
                                    <a href="../controller/exportar_csv_stock.php" class="btn btn-outline-success">
                                        <i class="fas fa-file-csv me-1"></i> Descargar CSV
                                    </a>
                                </div>
                                <table class="table table-striped">
                                    <thead>
                                        <tr><th>#</th><th>Producto</th><th>Stock</th><th>Precio</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stockActual as $p): ?>
                                            <tr>
                                                <td><?= $p["id"] ?></td>
                                                <td><?= htmlspecialchars($p["nombre"]) ?></td>
                                                <td>
                                                    <?php if ($p["stock"] <= 5): ?>
                                                        <span class="badge bg-danger"><?= $p["stock"] ?></span>
                                                    <?php else: ?>
                                                        <?= $p["stock"] ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>$<?= number_format($p["precio"], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Sistemas de Información Avanzada &mdash; UAE</div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script>
            const etiquetas = <?= $etiquetas ?>;
            const datos     = <?= $datos ?>;

            const ctx = document.getElementById("graficoVendidos").getContext("2d");
            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: etiquetas,
                    datasets: [{
                        label: "Unidades vendidas",
                        data: datos,
                        backgroundColor: "rgba(31, 122, 140, 0.7)"
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{ ticks: { beginAtZero: true } }]
                    }
                }
            });
        </script>
    </body>
</html>
