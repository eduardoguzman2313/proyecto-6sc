<?php
require_once "../model/verificar_sesion.php";
require_once "../model/ProveedorModel.php";
require_once "../model/ProductoModel.php";
require_once "../model/CompraModel.php";

// Solo el Admin registra compras
if (!tienePermiso("compras")) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permiso+para+ver+este+módulo");
    exit();
}

$modeloProveedor = new ProveedorModel();
$modeloProducto  = new ProductoModel();
$modeloCompra    = new CompraModel();

$proveedores = $modeloProveedor->obtenerTodos();
$productos   = $modeloProducto->obtenerTodos();

// ── Filtro opcional por rango de fechas ──
$fechaInicio = $_GET["fecha_inicio"] ?? "";
$fechaFin    = $_GET["fecha_fin"]    ?? "";

if (!empty($fechaInicio) && !empty($fechaFin)) {
    $compras = $modeloCompra->obtenerPorRangoFechas($fechaInicio, $fechaFin);
} else {
    $compras = $modeloCompra->obtenerTodas();
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
        <title>Compras — Mi Proyecto</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">

        <!-- NAVBAR -->
        <?php
        $rutaBase     = "../";
        $paginaActual = "compras.php";
        include "../partials/navbar.php";
        ?>

        <div id="layoutSidenav">
            <!-- SIDEBAR -->
            <?php include "../partials/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Compras a Proveedores</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Compras</li>
                        </ol>

                        <?php if ($msg === "ok"): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>¡Listo!</strong> Compra registrada. El stock del producto ya se actualizó.
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
                            <div class="card-header"><i class="fas fa-shopping-cart me-1"></i> Nueva Compra</div>
                            <div class="card-body">
                                <form action="../controller/guardar_compra.php" method="POST" onsubmit="return validarFormulario()">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Proveedor</label>
                                            <select class="form-select" name="id_proveedor" required>
                                                <option value="">-- Seleccione --</option>
                                                <?php foreach ($proveedores as $p): ?>
                                                    <option value="<?= $p["id"] ?>"><?= htmlspecialchars($p["nombre_comercial"]) ?> — <?= htmlspecialchars($p["nombre"]) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Producto</label>
                                            <select class="form-select" id="producto" name="id_producto" onchange="calcularTotal()" required>
                                                <option value="">-- Seleccione --</option>
                                                <?php foreach ($productos as $p): ?>
                                                    <option value="<?= $p["id"] ?>">
                                                        <?= htmlspecialchars($p["nombre"]) ?> (stock: <?= $p["stock"] ?? 0 ?> — precio venta actual: $<?= number_format($p["precio"], 2) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Cantidad</label>
                                            <input type="number" min="1" class="form-control" id="cantidad" name="cantidad" oninput="calcularTotal()" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Precio de costo (unitario)</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="precio_unitario" name="precio_unitario" oninput="calcularTotal()" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Margen de ganancia</label>
                                            <select class="form-select" id="margen_ganancia" name="margen_ganancia" onchange="calcularTotal()" required>
                                                <option value="">-- Seleccione --</option>
                                                <option value="10">10%</option>
                                                <option value="20">20%</option>
                                                <option value="30">30%</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Total de la compra</label>
                                            <input type="text" class="form-control" id="total_mostrado" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Precio de venta resultante</label>
                                            <input type="text" class="form-control bg-light" id="precio_venta_mostrado" readonly>
                                            <small class="text-muted">Este será el nuevo precio de venta del producto — se actualiza automáticamente al registrar la compra.</small>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Registrar Compra
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-table me-1"></i> Compras Registradas</div>
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
                                        <a href="compras.php" class="btn btn-outline-secondary">Limpiar</a>
                                    </div>
                                </form>

                                <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="badge bg-primary fs-6">Total: <?= count($compras) ?> compras</span>
                                    <div>
                                        <a href="reporte_compras.php?fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>
                                        <a href="../controller/exportar_csv_compras.php?fecha_inicio=<?= urlencode($fechaInicio) ?>&fecha_fin=<?= urlencode($fechaFin) ?>" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-file-csv me-1"></i> CSV
                                        </a>
                                    </div>
                                </div>
                                <table id="tablaCompras" class="table table-striped table-bordered">
                                    <thead>
                                        <tr><th>#</th><th>Proveedor</th><th>Producto</th><th>Cant.</th><th>P. Costo</th><th>Margen</th><th>P. Venta</th><th>Total</th><th>Registrado por</th><th>Fecha</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($compras)): ?>
                                            <tr><td colspan="10" class="text-center text-muted">No hay compras registradas aún.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($compras as $c): ?>
                                                <tr>
                                                    <td><?= $c["id"] ?></td>
                                                    <td><?= htmlspecialchars($c["proveedor"]) ?></td>
                                                    <td><?= htmlspecialchars($c["producto"]) ?></td>
                                                    <td><?= $c["cantidad"] ?></td>
                                                    <td>$<?= number_format($c["precio_unitario"], 2) ?></td>
                                                    <td><span class="badge bg-info text-dark"><?= $c["margen_ganancia"] ?>%</span></td>
                                                    <td>$<?= number_format($c["precio_venta"], 2) ?></td>
                                                    <td>$<?= number_format($c["total"], 2) ?></td>
                                                    <td><?= htmlspecialchars($c["usuario"]) ?></td>
                                                    <td><?= $c["fecha_compra"] ?></td>
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
                const cantidad = parseFloat(document.getElementById("cantidad").value) || 0;
                const precio   = parseFloat(document.getElementById("precio_unitario").value) || 0;
                const total    = cantidad * precio;

                document.getElementById("total_mostrado").value = "$" + total.toFixed(2);

                // ── Calcular el precio de venta con el margen elegido ──
                // precio_venta = precio_costo * (1 + margen/100)
                const margen = parseFloat(document.getElementById("margen_ganancia").value) || 0;
                const precioVenta = precio * (1 + (margen / 100));

                document.getElementById("precio_venta_mostrado").value =
                    margen > 0 ? "$" + precioVenta.toFixed(2) + " (costo $" + precio.toFixed(2) + " + " + margen + "%)" : "";
            }

            function validarFormulario() {
                return true;
            }

            const tablaCompras = document.getElementById("tablaCompras");
            if (tablaCompras) { new simpleDatatables.DataTable(tablaCompras); }
        </script>
    </body>
</html>
