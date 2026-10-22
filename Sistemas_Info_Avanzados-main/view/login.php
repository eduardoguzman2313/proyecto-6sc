<?php
// VIEW — login.php
// Programación 4: Aplicaciones Web — UAE
// ===========================
// Si el usuario YA inició sesión, no tiene sentido ver el login de nuevo
session_start();
if (isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
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
        <title>Acceso — Mi Proyecto</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header justify-content-center">
                                        <h3 class="fw-light my-2">Acceso al sistema</h3>
                                    </div>
                                    <div class="card-body">

                                        <?php if ($msg === "error"): ?>
                                            <div class="alert alert-danger">
                                                <?= htmlspecialchars($detalle) ?>
                                            </div>
                                        <?php elseif ($msg === "registrado"): ?>
                                            <div class="alert alert-success">
                                                ¡Cuenta creada! Ya puede iniciar sesión.
                                            </div>
                                        <?php elseif ($msg === "logout"): ?>
                                            <div class="alert alert-info">
                                                Sesión cerrada correctamente.
                                            </div>
                                        <?php elseif ($msg === "password_actualizada"): ?>
                                            <div class="alert alert-success">
                                                Contraseña actualizada. Ya puede iniciar sesión.
                                            </div>
                                        <?php elseif ($msg === "inactividad"): ?>
                                            <div class="alert alert-warning">
                                                Su sesión se cerró por inactividad. Vuelva a iniciar sesión.
                                            </div>
                                        <?php elseif ($msg === "no_autenticado"): ?>
                                            <div class="alert alert-warning">
                                                Debe iniciar sesión para continuar.
                                            </div>
                                        <?php endif; ?>

                                        <!-- VIEW → envía datos al CONTROLLER -->
                                        <form action="../controller/procesar_login.php" method="POST">
                                            <div class="mb-3">
                                                <label class="form-label" for="email">Correo electrónico</label>
                                                <input class="form-control" id="email" name="email" type="email" placeholder="correo@ejemplo.com" required />
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="password">Contraseña</label>
                                                <input class="form-control" id="password" name="password" type="password" placeholder="Ingrese su contraseña" required />
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <span class="small text-muted">¿Olvidó su contraseña? Solicite ayuda al administrador.</span>
                                                <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
    </body>
</html>
