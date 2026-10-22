<?php
// ===========================
// partials/sidebar.php
// Programación 4: Aplicaciones Web — UAE
// ===========================
// LÓGICA DE VISIBILIDAD (con permisos flexibles):
//   - SIN sesión iniciada -> Solo "Iniciar sesión"
//   - Con sesión          -> cada módulo se muestra SOLO si
//                            tienePermiso('modulo') es true
//                            (el Admin siempre tiene todos los permisos)
// Por si acaso este partial se carga sin haber pasado antes por
// verificar_sesion.php, nos aseguramos de tener la función disponible
if (!function_exists("tienePermiso")) {
    require_once __DIR__ . "/../model/permisos.php";
}

$autenticado = isset($_SESSION["usuario_id"]);

function esActiva($pagina, $actual) {
    return ($pagina === $actual) ? "active" : "";
}
?>
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <?php if (!$autenticado): ?>

                    <!-- ═══ SIN SESIÓN: solo se ofrece iniciar sesión ═══ -->
                    <div class="sb-sidenav-menu-heading">Acceso</div>
                    <a class="nav-link <?= esActiva('login.php', $paginaActual) ?>" href="<?= $rutaBase ?>view/login.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-sign-in-alt"></i></div>Iniciar sesión
                    </a>

                <?php else: ?>

                    <div class="sb-sidenav-menu-heading">Core</div>
                    <a class="nav-link <?= esActiva('index.php', $paginaActual) ?>" href="<?= $rutaBase ?>index.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>Dashboard
                    </a>

                    <?php if (tienePermiso("clientes") || tienePermiso("categorias") || tienePermiso("productos") ||
                              tienePermiso("proveedores") || tienePermiso("compras") || tienePermiso("ventas") ||
                              !empty($_SESSION["es_admin"])): ?>
                    <div class="sb-sidenav-menu-heading">Gestión</div>
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseGestion">
                        <div class="sb-nav-link-icon"><i class="fas fa-database"></i></div>
                        Registros
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse show" id="collapseGestion" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <?php if (tienePermiso("clientes")): ?>
                                <a class="nav-link <?= esActiva('clientes.php', $paginaActual) ?>" href="<?= $rutaBase ?>view/clientes.php">Clientes</a>
                            <?php endif; ?>

                            <?php if (tienePermiso("categorias")): ?>
                                <a class="nav-link <?= esActiva('categorias.php', $paginaActual) ?>" href="<?= $rutaBase ?>view/categorias.php">Categorías</a>
                            <?php endif; ?>

                            <?php if (tienePermiso("productos")): ?>
                                <a class="nav-link <?= esActiva('productos.php', $paginaActual) ?>" href="<?= $rutaBase ?>view/productos.php">Productos</a>
                            <?php endif; ?>

                            <?php if (tienePermiso("proveedores")): ?>
                                <a class="nav-link <?= esActiva('proveedores.php', $paginaActual) ?>" href="<?= $rutaBase ?>view/proveedores.php"><i class="fas fa-truck me-2"></i>Proveedores</a>
                            <?php endif; ?>

                            <?php if (tienePermiso("compras")): ?>
                                <a class="nav-link <?= esActiva('compras.php', $paginaActual) ?>" href="<?= $rutaBase ?>view/compras.php"><i class="fas fa-shopping-cart me-2"></i>Compras</a>
                            <?php endif; ?>

                            <?php if (tienePermiso("ventas")): ?>
                                <a class="nav-link <?= esActiva('ventas.php', $paginaActual) ?>" href="<?= $rutaBase ?>view/ventas.php"><i class="fas fa-cash-register me-2"></i>Ventas</a>
                            <?php endif; ?>

                            <?php if (!empty($_SESSION["es_admin"])): ?>
                                <a class="nav-link <?= esActiva('usuarios.php', $paginaActual) ?>" href="<?= $rutaBase ?>view/usuarios.php"><i class="fas fa-users-cog me-2"></i>Usuarios</a>
                            <?php endif; ?>
                        </nav>
                    </div>
                    <?php endif; ?>

                    <?php if (tienePermiso("dashboard") || tienePermiso("kardex")): ?>
                        <div class="sb-sidenav-menu-heading">Reportes</div>
                        <?php if (tienePermiso("dashboard")): ?>
                            <a class="nav-link <?= esActiva('dashboard.php', $paginaActual) ?>" href="<?= $rutaBase ?>view/dashboard.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-pie"></i></div>Stock de Productos
                            </a>
                        <?php endif; ?>
                        <?php if (tienePermiso("kardex")): ?>
                            <a class="nav-link <?= esActiva('kardex.php', $paginaActual) ?>" href="<?= $rutaBase ?>view/kardex.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>Inventario
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
        </div>
        <div class="sb-sidenav-footer">
            <?php if ($autenticado): ?>
                <div class="small">Sesión iniciada como:</div>
                <?= htmlspecialchars($_SESSION["nombre"]) ?>
                (<?= htmlspecialchars($_SESSION["rol"] ?? "") ?>)
            <?php else: ?>
                <div class="small">Sin sesión iniciada</div>
            <?php endif; ?>
        </div>
    </nav>
</div>
