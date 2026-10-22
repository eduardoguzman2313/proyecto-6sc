<?php
// ===========================
// guardar_venta.php — CONTROLLER
// Programación 4: Aplicaciones Web — UAE
// ===========================
require_once "../model/verificar_sesion.php";
require_once "../model/VentaModel.php";

// Este controlador ahora exige el permiso "ventas"
if (!tienePermiso("ventas")) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permiso+para+esta+acción");
    exit();
}


// Admin y Empleado pueden vender — cualquier usuario autenticado
// verificar_sesion.php ya se aseguró de que haya sesión activa

$id_cliente  = $_POST["id_cliente"]  ?? "";
$id_producto = $_POST["id_producto"] ?? "";
$cantidad    = $_POST["cantidad"]    ?? "";

if (empty($id_cliente) || empty($id_producto) || empty($cantidad)) {
    header("Location: ../view/ventas.php?msg=error&detalle=Complete+todos+los+campos");
    exit();
}

$id_usuario = $_SESSION["usuario_id"];

try {
    $modelo = new VentaModel();
    $modelo->registrarVenta($id_producto, $cantidad, $id_usuario, $id_cliente);

    header("Location: ../view/ventas.php?msg=ok");
    exit();

} catch (Exception $e) {
    // Aquí llega tanto el error de "stock insuficiente" como
    // cualquier error de base de datos — el mensaje se muestra tal cual
    header("Location: ../view/ventas.php?msg=error&detalle=" . urlencode($e->getMessage()));
    exit();
}
?>
