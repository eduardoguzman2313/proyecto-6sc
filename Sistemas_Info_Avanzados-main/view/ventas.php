<?php
require_once "../model/verificar_sesion.php";
require_once "../model/ClienteModel.php";
require_once "../model/ProductoModel.php";
require_once "../model/VentaModel.php";

// Admin y Empleado pueden vender

$modeloCliente  = new ClienteModel();
$modeloProducto = new ProductoModel();
$modeloVenta    = new VentaModel();

$clientes  = $modeloCliente->obtenerTodos();
$productos = $modeloProducto->obtenerTodos();
// ── Filtro opcional por rango de fechas ──
$fechaInicio = $_GET["fecha_inicio"] ?? "";
$fechaFin    = $_GET["fecha_fin"]    ?? "";

if (!empty($fechaInicio) && !empty($fechaFin)) {
    $ventas = $modeloVenta->obtenerPorRangoFechas($fechaInicio, $fechaFin);
} else {
    $ventas = $modeloVenta->obtenerTodas();
}

$msg     = $_GET["msg"]     ?? "";
$detalle = $_GET["detalle"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Ventas — Mi Proyecto</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">

        <!-- NAVBAR -->
        <?php
        $rutaBase     = "../";
        $paginaActual = "ventas.php";
        include "../partials/navbar.php";
        ?>

        <div id="layoutSidenav">
            <!-- SIDEBAR -->
            <?php include "../partials/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Ventas</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Ventas</li>
                        </ol>

                        <?php if ($msg === "ok"): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>¡Listo!</strong> Venta registrada. El stock del producto ya se actualizó.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif ($msg === "error"): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Error:</strong> <?= htmlspecialchars($detalle) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-cash-register me-1"></i> Nueva Venta</div>
                            <div class="card-body">
                                <form action="../controller/guardar_venta.php" method="POST">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Cliente</label>
                                            <select class="form-select" id="cliente" name="id_cliente" required>
                                                <option value="">-- Seleccione un cliente --</option>
                                                <?php foreach ($clientes as $c): ?>
                                                    <option value="<?= $c["id"] ?>">
                                                        <?= htmlspecialchars($c["nombre"] . " " . $c["apellido"]) ?>
                                                        — CI: <?= htmlspecialchars($c["cedula"]) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (empty($clientes)): ?>
                                                <small class="text-danger">
                                                    No hay clientes registrados. <a href="clientes.php">Registre uno primero</a>.
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Producto</label>
                                            <select class="form-select" id="producto" name="id_producto" onchange="calcularTotal()" required>
                                                <option value="">-- Seleccione --</option>
                                                <?php foreach ($productos as $p): ?>
                                                    <option value="<?= $p["id"] ?>"
                                                            data-precio="<?= $p["precio"] ?>"
                                                            data-stock="<?= $p["stock"] ?? 0 ?>">
                                                        <?= htmlspecialchars($p["nombre"]) ?> — $<?= number_format($p["precio"], 2) ?>
                                                        (disponible: <?= $p["stock"] ?? 0 ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Cantidad</label>
                                            <input type="number" min="1" class="form-control" id="cantidad" name="cantidad" oninput="calcularTotal()" required>
                                            <small id="stockDisponible" class="text-muted"></small>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Total (calculado)</label>
                                            <input type="text" class="form-control" id="total_mostrado" readonly>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Registrar Venta
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-table me-1"></i> Ventas Registradas</div>
                            <div class="card-body">
                                <!-- Filtro por rango de fechas -->
                                <form method="GET" class="row g-2 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Desde</label>
                                        <input type="date" class="form-control" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Hasta</label>
                                        <input type="date" class="form-control" name="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-secondary me-2"><i class="fas fa-filter me-1"></i> Filtrar</button>
                                        <a href="ventas.php" class="btn btn-outline-secondary">Limpiar</a>
                                    </div>
                                </form>

                                <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="badge bg-primary fs-6">Total: <?= count($ventas) ?> ventas</span>
                                    <div>
                                        <a href="reporte_ventas.php?fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>
                                        <a href="../controller/exportar_csv_ventas.php?fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?>" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-file-csv me-1"></i> CSV
                                        </a>
                                    </div>
                                </div>
                                <table id="tablaVentas" class="table table-striped table-bordered">
                                    <thead>
                                        <tr><th>#</th><th>Cliente</th><th>Producto</th><th>Cant.</th><th>P. Unit.</th><th>Total</th><th>Empleado</th><th>Fecha</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($ventas)): ?>
                                            <tr><td colspan="8" class="text-center text-muted">No hay ventas registradas aún.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($ventas as $v): ?>
                                                <tr>
                                                    <td><?= $v["id"] ?></td>
                                                    <td><?= htmlspecialchars($v["cliente"] . " " . $v["cliente_apellido"]) ?></td>
                                                    <td><?= htmlspecialchars($v["producto"]) ?></td>
                                                    <td><?= $v["cantidad"] ?></td>
                                                    <td>$<?= number_format($v["precio_unitario"], 2) ?></td>
                                                    <td>$<?= number_format($v["total"], 2) ?></td>
                                                    <td><?= htmlspecialchars($v["usuario"]) ?></td>
                                                    <td><?= $v["fecha_venta"] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
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
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
        <script>
            function calcularTotal() {
                const selectProducto = document.getElementById("producto");
                const opcionElegida  = selectProducto.options[selectProducto.selectedIndex];

                const precio = parseFloat(opcionElegida.getAttribute("data-precio")) || 0;
                const stock  = parseInt(opcionElegida.getAttribute("data-stock"))    || 0;
                const cantidad = parseFloat(document.getElementById("cantidad").value) || 0;

                const total = cantidad * precio;
                document.getElementById("total_mostrado").value = "$" + total.toFixed(2);

                const aviso = document.getElementById("stockDisponible");
                if (cantidad > stock) {
                    aviso.textContent = "⚠ Supera el stock disponible (" + stock + ")";
                    aviso.style.color = "red";
                } else {
                    aviso.textContent = "Stock disponible: " + stock;
                    aviso.style.color = "gray";
                }
            }

            const tablaVentas = document.getElementById("tablaVentas");
            if (tablaVentas) { new simpleDatatables.DataTable(tablaVentas); }
        </script>
    </body>
</html>
