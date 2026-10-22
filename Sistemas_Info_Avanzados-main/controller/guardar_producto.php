<?php
require_once "../model/verificar_sesion.php";  // Exige sesión activa antes de procesar

// Este controlador ahora exige el permiso "productos"
if (!tienePermiso("productos")) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permiso+para+esta+acción");
    exit();
}

// ===========================
// guardar_producto.php — CONTROLLER
// Programación 4: Aplicaciones Web — UAE
// ===========================

require_once "../model/ProductoModel.php";

$nombre       = trim($_POST["nombre"]       ?? "");
$id_categoria = trim($_POST["id_categoria"] ?? "");
$descripcion  = trim($_POST["descripcion"]  ?? "");
$precio       = trim($_POST["precio"]       ?? "");
$unidad       = trim($_POST["unidad"]       ?? "");

if (empty($nombre) || empty($id_categoria) || empty($precio) || empty($unidad)) {
    header("Location: ../view/productos.php?msg=error&detalle=Campos+obligatorios+incompletos");
    exit();
}

try {
    $modelo = new ProductoModel();
    $modelo->guardar($nombre, $id_categoria, $descripcion, $precio, $unidad);

    header("Location: ../view/productos.php?msg=ok");
    exit();

} catch (PDOException $e) {
    header("Location: ../view/productos.php?msg=error&detalle=" . urlencode($e->getMessage()));
    exit();
}
?>
