<?php
// VIEW — kardex.php
// Programación 4: Aplicaciones Web — UAE
// ===========================
// El Kardex es el "libro contable" del inventario: muestra, producto
// por producto, cada entrada (compra) y salida (venta) en orden
// cronológico, junto con el SALDO ACUMULADO después de cada movimiento.
//
// require_once "../model/verificar_sesion.php" ya viene incluido
// arriba por el layout general (ver bloque de navbar/sidebar abajo).

require_once "../model/verificar_sesion.php";
require_once "../model/ProductoModel.php";
require_once "../model/StockModel.php";

// Solo el Admin ve el Kardex
if (!tienePermiso("kardex")) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permiso+para+ver+este+módulo");
    exit();
}

$modeloProducto = new ProductoModel();
$modeloStock    = new StockModel();
$productos      = $modeloProducto->obtenerTodos();

$id_producto_seleccionado = $_GET["id_producto"] ?? "";
$movimientos = [];
$productoSeleccionado = null;

if (!empty($id_producto_seleccionado)) {
    $movimientos = $modeloStock->obtenerMovimientosPorProducto($id_producto_seleccionado);

    // Buscar el nombre del producto elegido para el título
    foreach ($productos as $p) {
        if ($p["id"] == $id_producto_seleccionado) {
            $productoSeleccionado = $p;
            break;
        }
    }
}

// ── Calcular el saldo acumulado ──
// Recorremos los movimientos EN ORDEN (del más viejo al más nuevo)
// y vamos sumando o restando según el tipo — así armamos la columna
// "Saldo" como lo haría un contador con lápiz y papel.
$saldoAcumulado = 0;
$filasKardex    = [];

foreach ($movimientos as $m) {
    if ($m["tipo"] === "entrada") {
        $saldoAcumulado += $m["cantidad"];
    } else {
        $saldoAcumulado -= $m["cantidad"];
    }

    $filasKardex[] = [
        "fecha"      => $m["fecha_movimiento"],
        "tipo"       => $m["tipo"],
        "referencia" => $m["referencia"],
        "cantidad"   => $m["cantidad"],
        "saldo"      => $saldoAcumulado,
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Inventario — Mi Proyecto</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">

        <!-- NAVBAR -->
        <?php
        $rutaBase     = "../";
        $paginaActual = "kardex.php";
        include "../partials/navbar.php";
        ?>

        <div id="layoutSidenav">
            <!-- SIDEBAR -->
            <?php include "../partials/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Inventario</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Kardex</li>
                        </ol>

                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-book me-1"></i> Seleccionar Producto</div>
                            <div class="card-body">
                                <form method="GET" class="row g-3">
                                    <div class="col-md-8">
                                        <select class="form-select" name="id_producto" onchange="this.form.submit()">
                                            <option value="">-- Seleccione un producto --</option>
                                            <?php foreach ($productos as $p): ?>
                                                <option value="<?= $p["id"] ?>" <?= ($p["id"] == $id_producto_seleccionado) ? "selected" : "" ?>>
                                                    <?= htmlspecialchars($p["nombre"]) ?> (stock actual: <?= $p["stock"] ?? 0 ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <?php if ($productoSeleccionado): ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-table me-1"></i>
                                    Movimientos de: <?= htmlspecialchars($productoSeleccionado["nombre"]) ?>
                                    — Stock actual: <strong><?= $productoSeleccionado["stock"] ?? 0 ?></strong>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Referencia</th>
                                                <th>Entrada</th>
                                                <th>Salida</th>
                                                <th>Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($filasKardex)): ?>
                                                <tr><td colspan="6" class="text-center text-muted">Este producto aún no tiene movimientos.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($filasKardex as $fila): ?>
                                                    <tr>
                                                        <td><?= $fila["fecha"] ?></td>
                                                        <td>
                                                            <?php if ($fila["tipo"] === "entrada"): ?>
                                                                <span class="badge bg-success">Entrada</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">Salida</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($fila["referencia"]) ?></td>
                                                        <td><?= $fila["tipo"] === "entrada" ? $fila["cantidad"] : "" ?></td>
                                                        <td><?= $fila["tipo"] === "salida"  ? $fila["cantidad"] : "" ?></td>
                                                        <td><strong><?= $fila["saldo"] ?></strong></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">Seleccione un producto para ver su Kardex.</div>
                        <?php endif; ?>

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
    </body>
</html>
