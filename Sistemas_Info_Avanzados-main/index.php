<?php
session_start();
$rutaBase     = "";
$paginaActual = "index.php";
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Panel - Mi Proyecto</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <?php include "partials/navbar.php"; ?>
        <div id="layoutSidenav">
            <?php include "partials/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <div class="text-center mt-5 pt-5">
                            <i class="fas fa-seedling fa-4x text-success mb-4"></i>
                            <h1 class="display-5">Mi Proyecto</h1>
                            <h3 class="fw-light text-muted mb-4">De aplicación web a sistema de información</h3>
                            <p class="lead">
                                Sistemas de Información Avanzada — Universidad Agraria del Ecuador
                            </p>
                            <?php if (!isset($_SESSION["usuario_id"])): ?>
                                <a href="view/login.php" class="btn btn-primary btn-lg mt-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                </a>
                            <?php else: ?>
                                <p class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Sesión activa como <?= htmlspecialchars($_SESSION["nombre"]) ?>
                                    (<?= htmlspecialchars($_SESSION["rol"]) ?>)
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Sistemas de Información Avanzada &mdash; UAE</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
