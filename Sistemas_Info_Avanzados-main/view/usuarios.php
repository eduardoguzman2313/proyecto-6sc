<?php
require_once "../model/verificar_sesion.php";
require_once "../model/UsuarioModel.php";

// Gestionar usuarios es SIEMPRE exclusivo del Administrador —
// a diferencia de los demás módulos, esto NO se delega con permisos,
// porque delegarlo permitiría que un usuario se dé a sí mismo más acceso.
if (empty($_SESSION["es_admin"])) {
    header("Location: ../index.php");
    exit();
}

$modelo   = new UsuarioModel();
$usuarios = $modelo->obtenerTodos();

// Lista de módulos que se pueden asignar como permiso a un usuario nuevo
$modulosDisponibles = [
    "clientes"    => "Clientes",
    "categorias"  => "Categorías",
    "productos"   => "Productos",
    "proveedores" => "Proveedores",
    "compras"     => "Compras",
    "ventas"      => "Ventas",
    "kardex"      => "Inventario (Kardex)",
    "dashboard"   => "Stock de Productos",
];

// Contadores rápidos para las tarjetas de resumen
$totalAdmins    = count(array_filter($usuarios, fn($u) => (int)$u["es_admin"] === 1));
$totalActivos   = count(array_filter($usuarios, fn($u) => $u["estado"] === "activo"));

$msg     = $_GET["msg"]     ?? "";
$detalle = $_GET["detalle"] ?? "";

// Si viene ?editar=ID, precargamos ese usuario para el formulario de edición
$usuarioEditar    = null;
$permisosEditando = [];
if (isset($_GET["editar"])) {
    $usuarioEditar = $modelo->obtenerPorId($_GET["editar"]);
    if ($usuarioEditar && !empty($usuarioEditar["permisos"])) {
        $permisosEditando = explode(",", $usuarioEditar["permisos"]);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Usuarios — Mi Proyecto</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">

        <!-- NAVBAR -->
        <?php
        $rutaBase     = "../";
        $paginaActual = "usuarios.php";
        include "../partials/navbar.php";
        ?>

        <div id="layoutSidenav">
            <!-- SIDEBAR -->
            <?php include "../partials/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Usuarios del Sistema</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Usuarios</li>
                        </ol>

                        <?php if ($msg === "ok"): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i><strong>¡Listo!</strong> Usuario creado correctamente.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif ($msg === "actualizado"): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i><strong>¡Listo!</strong> Usuario actualizado correctamente.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif ($msg === "error"): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i><strong>Error:</strong> <?= htmlspecialchars($detalle) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Tarjetas de resumen -->
                        <div class="row">
                            <div class="col-xl-4 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">Total: <?= count($usuarios) ?></div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="card bg-danger text-white mb-4">
                                    <div class="card-body">Administradores: <?= $totalAdmins ?></div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="card bg-success text-white mb-4">
                                    <div class="card-body">Activos: <?= $totalActivos ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- ═══ Formulario: Crear nuevo usuario ═══ -->
                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-user-plus me-1"></i> Nuevo Usuario</div>
                            <div class="card-body">
                                <form action="../controller/procesar_registro.php" method="POST">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Cédula</label>
                                            <input type="text" class="form-control" name="cedula" maxlength="10" required>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label">Nombres completos</label>
                                            <input type="text" class="form-control" name="nombre" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Dirección</label>
                                            <input type="text" class="form-control" name="direccion">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" class="form-control" name="telefono" maxlength="10" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Cargo / Rol</label>
                                            <input type="text" class="form-control" name="rol"
                                                   placeholder="Ej: Bodeguero, Vendedor, Contador" required>
                                            <small class="text-muted">Es solo una etiqueta — el acceso real se define abajo.</small>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Correo electrónico</label>
                                            <input type="email" class="form-control" name="email" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Contraseña</label>
                                            <input type="password" class="form-control" name="password" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="es_admin"
                                                       value="1" id="esAdminNuevo" onchange="togglePermisos('nuevo')">
                                                <label class="form-check-label fw-bold" for="esAdminNuevo">
                                                    Es Administrador (acceso total, ignora los permisos de abajo)
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3" id="bloquePermisosNuevo">
                                        <label class="form-label fw-bold">Permisos — ¿a qué módulos tiene acceso?</label>
                                        <?php foreach ($modulosDisponibles as $clave => $etiqueta): ?>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permisos[]"
                                                           value="<?= $clave ?>" id="permiso_nuevo_<?= $clave ?>">
                                                    <label class="form-check-label" for="permiso_nuevo_<?= $clave ?>">
                                                        <?= $etiqueta ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Crear Usuario
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- ═══ Formulario: Editar usuario (solo aparece si viene ?editar=ID) ═══ -->
                        <?php if ($usuarioEditar): ?>
                            <div class="card mb-4 border-warning">
                                <div class="card-header bg-warning">
                                    <i class="fas fa-user-edit me-1"></i> Editando a: <?= htmlspecialchars($usuarioEditar["nombre"]) ?>
                                </div>
                                <div class="card-body">
                                    <form action="../controller/actualizar_usuario.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $usuarioEditar["id"] ?>">
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Cédula</label>
                                                <input type="text" class="form-control" name="cedula" maxlength="10"
                                                       value="<?= htmlspecialchars($usuarioEditar["cedula"]) ?>" required>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label">Nombres completos</label>
                                                <input type="text" class="form-control" name="nombre"
                                                       value="<?= htmlspecialchars($usuarioEditar["nombre"]) ?>" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Dirección</label>
                                                <input type="text" class="form-control" name="direccion"
                                                       value="<?= htmlspecialchars($usuarioEditar["direccion"] ?? "") ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Teléfono</label>
                                                <input type="text" class="form-control" name="telefono" maxlength="10"
                                                       value="<?= htmlspecialchars($usuarioEditar["telefono"]) ?>" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Cargo / Rol</label>
                                                <input type="text" class="form-control" name="rol"
                                                       value="<?= htmlspecialchars($usuarioEditar["rol"]) ?>" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Correo electrónico</label>
                                                <input type="email" class="form-control" name="email"
                                                       value="<?= htmlspecialchars($usuarioEditar["email"]) ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Estado</label>
                                                <select class="form-select" name="estado" required>
                                                    <option value="activo" <?= $usuarioEditar["estado"] === "activo" ? "selected" : "" ?>>Activo</option>
                                                    <option value="inactivo" <?= $usuarioEditar["estado"] === "inactivo" ? "selected" : "" ?>>Inactivo</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="es_admin"
                                                           value="1" id="esAdminEditar" onchange="togglePermisos('editar')"
                                                           <?= !empty($usuarioEditar["es_admin"]) ? "checked" : "" ?>>
                                                    <label class="form-check-label fw-bold" for="esAdminEditar">
                                                        Es Administrador (acceso total, ignora los permisos de abajo)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3" id="bloquePermisosEditar">
                                            <label class="form-label fw-bold">Permisos — ¿a qué módulos tiene acceso?</label>
                                            <?php foreach ($modulosDisponibles as $clave => $etiqueta): ?>
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permisos[]"
                                                               value="<?= $clave ?>" id="permiso_editar_<?= $clave ?>"
                                                               <?= in_array($clave, $permisosEditando) ? "checked" : "" ?>>
                                                        <label class="form-check-label" for="permiso_editar_<?= $clave ?>">
                                                            <?= $etiqueta ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-save me-1"></i> Guardar Cambios
                                        </button>
                                        <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- ═══ Tabla de usuarios ═══ -->
                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-table me-1"></i> Lista de Usuarios</div>
                            <div class="card-body">
                                <table id="tablaUsuarios" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th><th>Cédula</th><th>Nombre</th><th>Email</th>
                                            <th>Teléfono</th><th>Cargo</th><th>Permisos</th><th>Estado</th><th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($usuarios)): ?>
                                            <tr><td colspan="9" class="text-center text-muted">No hay usuarios registrados aún.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($usuarios as $u): ?>
                                                <tr>
                                                    <td><?= $u["id"] ?></td>
                                                    <td><?= htmlspecialchars($u["cedula"]) ?></td>
                                                    <td><?= htmlspecialchars($u["nombre"]) ?></td>
                                                    <td><?= htmlspecialchars($u["email"]) ?></td>
                                                    <td><?= htmlspecialchars($u["telefono"]) ?></td>
                                                    <td><?= htmlspecialchars($u["rol"]) ?></td>
                                                    <td>
                                                        <?php if (!empty($u["es_admin"])): ?>
                                                            <span class="badge bg-danger">Acceso total</span>
                                                        <?php elseif (!empty($u["permisos"])): ?>
                                                            <?php foreach (explode(",", $u["permisos"]) as $p): ?>
                                                                <span class="badge bg-info text-dark"><?= htmlspecialchars($modulosDisponibles[$p] ?? $p) ?></span>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">Sin permisos</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($u["estado"] === "activo"): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="usuarios.php?editar=<?= $u["id"] ?>" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                    </td>
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
            const tablaUsuarios = document.getElementById("tablaUsuarios");
            if (tablaUsuarios) { new simpleDatatables.DataTable(tablaUsuarios); }

            // Si marcan "Es Administrador", ocultamos los checkboxes de permisos
            // porque el Admin ya tiene acceso a todo — no hace falta marcarlos uno por uno.
            function togglePermisos(formulario) {
                const checkbox = document.getElementById("esAdmin" + (formulario === "nuevo" ? "Nuevo" : "Editar"));
                const bloque   = document.getElementById("bloquePermisos" + (formulario === "nuevo" ? "Nuevo" : "Editar"));
                bloque.style.display = checkbox.checked ? "none" : "flex";
            }
        </script>
    </body>
</html>
