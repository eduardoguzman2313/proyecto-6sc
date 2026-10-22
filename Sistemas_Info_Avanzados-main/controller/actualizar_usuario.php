<?php
// ===========================
// actualizar_usuario.php — CONTROLLER
// Programación 4: Aplicaciones Web — UAE
// ===========================
require_once "../model/verificar_sesion.php";
require_once "../model/UsuarioModel.php";

if (empty($_SESSION["es_admin"])) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permisos+para+esta+acción");
    exit();
}

$id        = $_POST["id"]        ?? "";
$cedula    = trim($_POST["cedula"]    ?? "");
$nombre    = trim($_POST["nombre"]    ?? "");
$direccion = trim($_POST["direccion"] ?? "");
$email     = trim($_POST["email"]     ?? "");
$telefono  = trim($_POST["telefono"]  ?? "");
$rol       = trim($_POST["rol"]       ?? "");
$estado    = trim($_POST["estado"]    ?? "activo");
$esAdmin   = isset($_POST["es_admin"]);
$permisos  = $_POST["permisos"]       ?? [];

if (empty($id) || empty($cedula) || empty($nombre) || empty($email) || empty($telefono) || empty($rol)) {
    header("Location: ../view/usuarios.php?msg=error&detalle=Todos+los+campos+son+obligatorios");
    exit();
}

try {
    $modelo = new UsuarioModel();
    $modelo->actualizar($id, $cedula, $nombre, $direccion, $email, $telefono, $rol, $esAdmin, $permisos, $estado);

    header("Location: ../view/usuarios.php?msg=actualizado");
    exit();

} catch (PDOException $e) {
    header("Location: ../view/usuarios.php?msg=error&detalle=" . urlencode($e->getMessage()));
    exit();
}
?>
