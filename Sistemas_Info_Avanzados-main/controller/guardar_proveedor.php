<?php
// ===========================
// guardar_proveedor.php — CONTROLLER
// Programación 4: Aplicaciones Web — UAE
// ===========================
require_once "../model/verificar_sesion.php";
require_once "../model/ProveedorModel.php";

// Solo el Administrador maneja proveedores — la llave maestra del edificio
if (!tienePermiso("proveedores")) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permiso+para+esta+acción");
    exit();
}

$nombre_comercial = trim($_POST["nombre_comercial"] ?? "");
$nombre    = trim($_POST["nombre"]    ?? "");
$telefono  = trim($_POST["telefono"]  ?? "");
$email     = trim($_POST["email"]     ?? "");
$direccion = trim($_POST["direccion"] ?? "");

if (empty($nombre_comercial) || empty($nombre) || empty($telefono)) {
    header("Location: ../view/proveedores.php?msg=error&detalle=Nombre+comercial,+contacto+y+teléfono+son+obligatorios");
    exit();
}

try {
    $modelo = new ProveedorModel();
    $modelo->guardar($nombre_comercial, $nombre, $telefono, $email, $direccion);

    header("Location: ../view/proveedores.php?msg=ok");
    exit();

} catch (PDOException $e) {
    header("Location: ../view/proveedores.php?msg=error&detalle=" . urlencode($e->getMessage()));
    exit();
}
?>
