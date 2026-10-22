<?php
// VIEW — password.php
// Sistemas de Información Avanzada — UAE
// ===========================
// Cambio de contraseña del usuario que tiene la sesión iniciada.
// Si alguien no ha iniciado sesión, verificar_sesion.php lo envía al login.
require_once "../model/verificar_sesion.php";
$msg     = $_GET["msg"]     ?? "";
$detalle = $_GET["detalle"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Cambiar Contraseña — Mi Proyecto</title>
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
                                        <h3 class="fw-light my-2">Cambiar Contraseña</h3>
                                    </div>
                                    <div class="card-body">

                                        <?php if ($msg === "error"): ?>
                                            <div class="alert alert-danger"><?= htmlspecialchars($detalle) ?></div>
                                        <?php elseif ($msg === "ok"): ?>
                                            <div class="alert alert-success">Su contraseña se actualizó correctamente.</div>
                                        <?php endif; ?>

                                        <div class="small mb-3 text-muted">
                                            Usuario: <strong><?= htmlspecialchars($_SESSION["nombre"]) ?></strong>.
                                            Escriba su contraseña actual y la nueva (mínimo 8 caracteres).
                                        </div>

                                        <form action="../controller/procesar_password.php" method="POST" onsubmit="return validarCoincidencia()">
                                            <div class="mb-3">
                                                <label class="form-label" for="password_actual">Contraseña actual</label>
                                                <input class="form-control" id="password_actual" name="password_actual" type="password" placeholder="Contraseña actual" required />
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="password_nueva">Nueva contraseña</label>
                                                <input class="form-control" id="password_nueva" name="password_nueva" type="password" placeholder="Nueva contraseña" minlength="8" required />
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="password_confirma">Confirmar contraseña</label>
                                                <input class="form-control" id="password_confirma" name="password_confirma" type="password" placeholder="Repita la contraseña" required />
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <a class="small" href="../index.php">Volver al panel</a>
                                                <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
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
        <script>
            // Validación en el navegador: aviso inmediato si no coinciden.
            // El servidor (procesar_password.php) SIEMPRE vuelve a validar,
            // porque JavaScript se puede desactivar.
            function validarCoincidencia() {
                const nueva    = document.getElementById("password_nueva").value;
                const confirma = document.getElementById("password_confirma").value;

                if (nueva !== confirma) {
                    alert("Las contraseñas no coinciden. Verifique e intente de nuevo.");
                    return false;
                }
                return true;
            }
        </script>
    </body>
</html>
