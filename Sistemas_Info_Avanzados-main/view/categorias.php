<?php
require_once "../model/verificar_sesion.php";  // Protege esta vista: exige sesión activa

require_once "../model/CategoriaModel.php";
// Solo el Admin gestiona el catálogo (categorías/productos)
if (!tienePermiso("categorias")) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permiso+para+ver+este+módulo");
    exit();
}

$modelo     = new CategoriaModel();
$categorias = $modelo->obtenerTodas();

$msg     = $_GET["msg"]     ?? "";
$detalle = $_GET["detalle"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Categorías — Mi Proyecto</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">

        <?php
        $rutaBase     = "../";
        $paginaActual = "categorias.php";
        include "../partials/navbar.php";
        ?>

        <div id="layoutSidenav">
            <?php include "../partials/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Categorías</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Categorías</li>
                        </ol>

                        <?php if ($msg === "ok"): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>¡Listo!</strong> Categoría registrada correctamente.
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
                            <div class="card-header">
                                <i class="fas fa-tags me-1"></i> Nueva Categoría
                            </div>
                            <div class="card-body">
                                <form action="../controller/guardar_categoria.php" method="POST">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="nombre" class="form-label">Nombre de la Categoría</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Electrónica" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Descripción opcional">
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Guardar Categoría
                                        </button>
                                        <button type="reset" class="btn btn-secondary ms-2">
                                            <i class="fas fa-eraser me-1"></i> Limpiar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i> Categorías Registradas
                            </div>
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="badge bg-primary fs-6">Total registrados: <?= count($categorias) ?></span>
                                    <div>
                                        <a href="reporte_categorias.php" class="btn btn-outline-primary btn-sm" target="_blank">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>
                                        <a href="../controller/exportar_csv_categorias.php" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-file-csv me-1"></i> CSV
                                        </a>
                                    </div>
                                </div>
                                <table id="tablaCategorias" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($categorias)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No hay categorías registradas aún.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($categorias as $cat): ?>
                                                <tr>
                                                    <td><?= $cat["id"] ?></td>
                                                    <td><?= htmlspecialchars($cat["nombre"]) ?></td>
                                                    <td><?= htmlspecialchars($cat["descripcion"]) ?></td>
                                                    <td><?= $cat["fecha_registro"] ?></td>
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
            const tablaCategorias = document.getElementById("tablaCategorias");
            if (tablaCategorias) { new simpleDatatables.DataTable(tablaCategorias); }
        </script>
    </body>
</html>
