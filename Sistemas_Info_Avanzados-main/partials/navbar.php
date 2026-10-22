<?php
// ===========================
// partials/navbar.php
// Sistemas de Información Avanzada — UAE
// ===========================
?>
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="<?= $rutaBase ?>index.php">Mi Proyecto</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <div class="input-group">
            <input class="form-control" type="text" placeholder="Buscar..." />
            <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
        </div>
    </form>
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <?php if (isset($_SESSION["usuario_id"])): ?>
            <!-- Usuario CON sesión activa: mostramos su nombre y la opción de salir -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user fa-fw"></i> <?= htmlspecialchars($_SESSION["nombre"]) ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= $rutaBase ?>view/password.php"><i class="fas fa-key fa-fw me-1"></i>Cambiar contraseña</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item" href="<?= $rutaBase ?>controller/logout.php">Cerrar sesión</a></li>
                </ul>
            </li>
        <?php else: ?>
            <!-- Usuario SIN sesión: mostramos un botón directo para entrar -->
            <li class="nav-item">
                <a class="nav-link" href="<?= $rutaBase ?>view/login.php">
                    <i class="fas fa-sign-in-alt me-1"></i> Iniciar sesión
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
