<?php
// ===========================
// procesar_password.php — CONTROLLER
// Sistemas de Información Avanzada — UAE
// ===========================
// Cambia la contraseña del usuario QUE TIENE LA SESIÓN INICIADA.
// Ya no se puede cambiar la contraseña de otra persona escribiendo su correo:
// el usuario se toma de la sesión y se exige la contraseña actual.

session_start();
require_once "../model/UsuarioModel.php";

// 1. Solo usuarios con sesión iniciada
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../view/login.php?msg=no_autenticado");
    exit();
}

// 2. Leer el formulario
$passwordActual  = $_POST["password_actual"]   ?? "";
$passwordNueva   = $_POST["password_nueva"]    ?? "";
$passwordConfirm = $_POST["password_confirma"] ?? "";

if ($passwordActual === "" || $passwordNueva === "") {
    header("Location: ../view/password.php?msg=error&detalle=Complete+todos+los+campos");
    exit();
}
if ($passwordNueva !== $passwordConfirm) {
    header("Location: ../view/password.php?msg=error&detalle=Las+contraseñas+nuevas+no+coinciden");
    exit();
}
if (strlen($passwordNueva) < 8) {
    header("Location: ../view/password.php?msg=error&detalle=La+nueva+contraseña+debe+tener+al+menos+8+caracteres");
    exit();
}

// 3. Cambiar SOLO la contraseña del usuario de la sesión
$modelo    = new UsuarioModel();
$resultado = $modelo->cambiarPasswordPropia($_SESSION["usuario_id"], $passwordActual, $passwordNueva);

if ($resultado === "ok") {
    header("Location: ../view/password.php?msg=ok");
} elseif ($resultado === "actual_incorrecta") {
    header("Location: ../view/password.php?msg=error&detalle=La+contraseña+actual+es+incorrecta");
} else {
    header("Location: ../view/password.php?msg=error&detalle=No+se+pudo+actualizar+la+contraseña");
}
exit();
?>
