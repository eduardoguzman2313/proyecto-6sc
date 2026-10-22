<?php
require_once "../model/verificar_sesion.php";
require_once "../model/ProveedorModel.php";

// Solo el Admin ve esta pantalla
if (!tienePermiso("proveedores")) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permiso+para+ver+este+módulo");
    exit();
}

$modelo      = new ProveedorModel();
$proveedores = $modelo->obtenerTodos();

$msg     = $_GET["msg"]     ?? "";
$detalle = $_GET["detalle"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Proveedores — Mi Proyecto</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">

        <!-- NAVBAR -->
        <?php
        $rutaBase     = "../";
        $paginaActual = "proveedores.php";
        include "../partials/navbar.php";
        ?>

        <div id="layoutSidenav">
            <!-- SIDEBAR -->
            <?php include "../partials/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Proveedores</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Proveedores</li>
                        </ol>

                        <?php if ($msg === "ok"): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>¡Listo!</strong> Proveedor registrado correctamente.
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
                            <div class="card-header"><i class="fas fa-truck me-1"></i> Nuevo Proveedor</div>
                            <div class="card-body">
                                <form action="../controller/guardar_proveedor.php" method="POST">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nombre Comercial (Empresa)</label>
                                            <input type="text" class="form-control" name="nombre_comercial" placeholder="Ej: Cartimex" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Nombre del Contacto</label>
                                            <input type="text" class="form-control" name="nombre" placeholder="Ej: Pedro Salinas" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" class="form-control" name="telefono" maxlength="10" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Dirección</label>
                                            <input type="text" class="form-control" name="direccion">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Guardar Proveedor
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-table me-1"></i> Proveedores Registrados</div>
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="badge bg-primary fs-6">Total registrados: <?= count($proveedores) ?></span>
                                    <div>
                                        <a href="reporte_proveedores.php" class="btn btn-outline-primary btn-sm" target="_blank">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>
                                        <a href="../controller/exportar_csv_proveedores.php" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-file-csv me-1"></i> CSV
                                        </a>
                                    </div>
                                </div>
                                <table id="tablaProveedores" class="table table-striped table-bordered">
                                    <thead>
                                        <tr><th>#</th><th>Nombre Comercial</th><th>Contacto</th><th>Teléfono</th><th>Email</th><th>Dirección</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($proveedores)): ?>
                                            <tr><td colspan="6" class="text-center text-muted">No hay proveedores registrados aún.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($proveedores as $p): ?>
                                                <tr>
                                                    <td><?= $p["id"] ?></td>
                                                    <td><strong><?= htmlspecialchars($p["nombre_comercial"]) ?></strong></td>
                                                    <td><?= htmlspecialchars($p["nombre"]) ?></td>
                                                    <td><?= htmlspecialchars($p["telefono"]) ?></td>
                                                    <td><?= htmlspecialchars($p["email"]) ?></td>
                                                    <td><?= htmlspecialchars($p["direccion"]) ?></td>
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
            const tablaProveedores = document.getElementById("tablaProveedores");
            if (tablaProveedores) { new simpleDatatables.DataTable(tablaProveedores); }
        </script>
    </body>
</html>
