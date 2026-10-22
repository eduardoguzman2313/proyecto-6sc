<?php
require_once "../model/verificar_sesion.php";  // Protege esta vista: exige sesión activa

require_once "../model/ProductoModel.php";
require_once "../model/CategoriaModel.php";

// Solo el Admin gestiona el catálogo (categorías/productos)
if (!tienePermiso("productos")) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permiso+para+ver+este+módulo");
    exit();
}

$modeloProd = new ProductoModel();
$modeloCat  = new CategoriaModel();
$productos  = $modeloProd->obtenerTodos();
$categorias = $modeloCat->obtenerTodas();

$msg     = $_GET["msg"]     ?? "";
$detalle = $_GET["detalle"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Productos — Mi Proyecto</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">

        <?php
        $rutaBase     = "../";
        $paginaActual = "productos.php";
        include "../partials/navbar.php";
        ?>

        <div id="layoutSidenav">
            <?php include "../partials/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Productos</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Productos</li>
                        </ol>

                        <?php if ($msg === "ok"): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>¡Listo!</strong> Producto registrado correctamente.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif ($msg === "error"): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Error:</strong> <?= htmlspecialchars($detalle) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($categorias)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Antes de registrar un producto, debe registrar al menos una
                                <a href="categorias.php" class="alert-link">Categoría</a>.
                            </div>
                        <?php endif; ?>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-box me-1"></i> Nuevo Producto
                            </div>
                            <div class="card-body">
                                <form action="../controller/guardar_producto.php" method="POST">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="nombre" class="form-label">Nombre del Producto</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Laptop HP" required>
                                        </div>
                                        <div class="col-md-6">
                                            <!-- El select se llena dinámicamente desde la BD via PHP -->
                                            <label for="id_categoria" class="form-label">Categoría</label>
                                            <select class="form-select" id="id_categoria" name="id_categoria" required>
                                                <option value="">-- Seleccione una categoría --</option>
                                                <?php foreach ($categorias as $cat): ?>
                                                    <option value="<?= $cat["id"] ?>">
                                                        <?= htmlspecialchars($cat["nombre"]) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2" placeholder="Descripción opcional"></textarea>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="precio" class="form-label">Precio</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" min="0" class="form-control" id="precio" name="precio" placeholder="0.00" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="unidad" class="form-label">Unidad</label>
                                            <select class="form-select" id="unidad" name="unidad" required>
                                                <option value="">-- Seleccione --</option>
                                                <option value="unidad">Unidad</option>
                                                <option value="kg">Kilogramo (kg)</option>
                                                <option value="litro">Litro</option>
                                                <option value="metro">Metro</option>
                                                <option value="caja">Caja</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Guardar Producto
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
                                <i class="fas fa-table me-1"></i> Productos Registrados
                            </div>
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="badge bg-primary fs-6">Total registrados: <?= count($productos) ?></span>
                                    <div>
                                        <a href="reporte_productos.php" class="btn btn-outline-primary btn-sm" target="_blank">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>
                                        <a href="../controller/exportar_csv_productos.php" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-file-csv me-1"></i> CSV
                                        </a>
                                    </div>
                                </div>
                                <table id="tablaProductos" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Categoría</th>
                                            <th>Descripción</th>
                                            <th>Precio</th>
                                            <th>Unidad</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($productos)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No hay productos registrados aún.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($productos as $p): ?>
                                                <tr>
                                                    <td><?= $p["id"] ?></td>
                                                    <td><?= htmlspecialchars($p["nombre"]) ?></td>
                                                    <td><?= htmlspecialchars($p["categoria"]) ?></td>
                                                    <td><?= htmlspecialchars($p["descripcion"]) ?></td>
                                                    <td>$<?= number_format($p["precio"], 2) ?></td>
                                                    <td><?= htmlspecialchars($p["unidad"]) ?></td>
                                                    <td><?= $p["fecha_registro"] ?></td>
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
            const tablaProductos = document.getElementById("tablaProductos");
            if (tablaProductos) { new simpleDatatables.DataTable(tablaProductos); }
        </script>
    </body>
</html>
