<?php
require_once "../model/verificar_sesion.php";  // Protege esta vista: exige sesión activa

// VIEW — clientes.php
// Programación 4: Aplicaciones Web — UAE
// ===========================
// La vista NO hace consultas directamente.
// Usa el MODEL para obtener los datos que mostrará.

require_once "../model/ClienteModel.php";
$modelo   = new ClienteModel();
$clientes = $modelo->obtenerTodos();

$msg     = $_GET["msg"]     ?? "";
$detalle = $_GET["detalle"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Clientes — Mi Proyecto</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">

        <!-- NAVBAR -->
        <?php
        $rutaBase     = "../";
        $paginaActual = "clientes.php";
        include "../partials/navbar.php";
        ?>

        <div id="layoutSidenav">
            <!-- SIDEBAR -->
            <?php include "../partials/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Clientes</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Clientes</li>
                        </ol>

                        <!-- Alerta de resultado -->
                        <?php if ($msg === "ok"): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>¡Listo!</strong> Cliente registrado correctamente.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif ($msg === "error"): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Error:</strong> <?= htmlspecialchars($detalle) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario de registro -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-user-plus me-1"></i> Nuevo Cliente
                            </div>
                            <div class="card-body">
                                <!--
                                    VIEW → envía datos al CONTROLLER
                                    El controller usa el MODEL (POO) para guardar en MySQL
                                -->
                                <form action="../controller/guardar_cliente.php" method="POST">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="nombre" class="form-label">Nombre</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese el nombre" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="apellido" class="form-label">Apellido</label>
                                            <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ingrese el apellido" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="cedula" class="form-label">Cédula</label>
                                            <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Ej: 0912345678" maxlength="10" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="telefono" class="form-label">Teléfono</label>
                                            <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ej: 0991234567" maxlength="10" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="correo@ejemplo.com" required>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Guardar Cliente
                                        </button>
                                        <button type="reset" class="btn btn-secondary ms-2">
                                            <i class="fas fa-eraser me-1"></i> Limpiar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Tabla de clientes registrados -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i> Clientes Registrados
                            </div>
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
                                    <span class="badge bg-primary fs-6">Total registrados: <?= count($clientes) ?></span>
                                    <div>
                                        <a href="reporte_clientes.php" class="btn btn-outline-primary btn-sm" target="_blank">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>
                                        <a href="../controller/exportar_csv_clientes.php" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-file-csv me-1"></i> CSV
                                        </a>
                                    </div>
                                </div>
                                <table id="tablaClientes" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Cédula</th>
                                            <th>Teléfono</th>
                                            <th>Email</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($clientes)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">
                                                    No hay clientes registrados aún.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($clientes as $c): ?>
                                                <tr>
                                                    <td><?= $c["id"] ?></td>
                                                    <td><?= htmlspecialchars($c["nombre"]) ?></td>
                                                    <td><?= htmlspecialchars($c["apellido"]) ?></td>
                                                    <td><?= htmlspecialchars($c["cedula"]) ?></td>
                                                    <td><?= htmlspecialchars($c["telefono"]) ?></td>
                                                    <td><?= htmlspecialchars($c["email"]) ?></td>
                                                    <td><?= $c["fecha_registro"] ?></td>
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
            // Activar DataTables en la tabla de clientes
            const tablaClientes = document.getElementById("tablaClientes");
            if (tablaClientes) { new simpleDatatables.DataTable(tablaClientes); }
        </script>
    </body>
</html>
