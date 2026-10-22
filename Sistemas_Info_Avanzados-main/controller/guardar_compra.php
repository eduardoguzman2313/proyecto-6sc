<?php
// ===========================
// guardar_compra.php — CONTROLLER
// Programación 4: Aplicaciones Web — UAE
// ===========================
require_once "../model/verificar_sesion.php";
require_once "../model/CompraModel.php";

// Solo el Administrador puede registrar compras
if (!tienePermiso("compras")) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permiso+para+esta+acción");
    exit();
}

$id_proveedor     = $_POST["id_proveedor"]     ?? "";
$id_producto      = $_POST["id_producto"]      ?? "";
$cantidad         = $_POST["cantidad"]         ?? "";
$precio_unitario  = $_POST["precio_unitario"]  ?? "";
$margen_ganancia  = $_POST["margen_ganancia"]  ?? "";

if (empty($id_proveedor) || empty($id_producto) || empty($cantidad) || empty($precio_unitario) || $margen_ganancia === "") {
    header("Location: ../view/compras.php?msg=error&detalle=Complete+todos+los+campos");
    exit();
}

// El usuario que registra la compra es quien tiene la sesión abierta
$id_usuario = $_SESSION["usuario_id"];

try {
    $modelo = new CompraModel();
    $modelo->registrarCompra($id_proveedor, $id_producto, $cantidad, $precio_unitario, $id_usuario, $margen_ganancia);

    header("Location: ../view/compras.php?msg=ok");
    exit();

} catch (PDOException $e) {
    header("Location: ../view/compras.php?msg=error&detalle=" . urlencode($e->getMessage()));
    exit();
}
?>
