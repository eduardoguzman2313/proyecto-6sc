<?php
// ===========================
// procesar_registro.php — CONTROLLER
// Programación 4: Aplicaciones Web — UAE
// ===========================
// Ya NO es un registro público. Ahora solo el Administrador puede
// crear cuentas nuevas (Admin o Empleado) desde la pantalla de Usuarios.

require_once "../model/verificar_sesion.php";
require_once "../model/UsuarioModel.php";

if (empty($_SESSION["es_admin"])) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permisos+para+crear+usuarios");
    exit();
}

$cedula    = trim($_POST["cedula"]    ?? "");
$nombre    = trim($_POST["nombre"]    ?? "");
$direccion = trim($_POST["direccion"] ?? "");
$email     = trim($_POST["email"]     ?? "");
$telefono  = trim($_POST["telefono"]  ?? "");
$password  = trim($_POST["password"]  ?? "");
$rol       = trim($_POST["rol"]       ?? "");
$esAdmin   = isset($_POST["es_admin"]);           // el checkbox solo llega si está marcado
$permisos  = $_POST["permisos"]       ?? [];       // arreglo de módulos marcados

if (empty($cedula) || empty($nombre) || empty($email) || empty($telefono) || empty($password) || empty($rol)) {
    header("Location: ../view/usuarios.php?msg=error&detalle=Todos+los+campos+son+obligatorios");
    exit();
}

try {
    $modelo = new UsuarioModel();
    $modelo->registrar($cedula, $nombre, $direccion, $email, $telefono, $password, $rol, $esAdmin, $permisos);

    header("Location: ../view/usuarios.php?msg=ok");
    exit();

} catch (PDOException $e) {
    header("Location: ../view/usuarios.php?msg=error&detalle=" . urlencode("Ese email ya está registrado"));
    exit();
}
?>
