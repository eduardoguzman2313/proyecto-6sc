<?php
require_once "../model/verificar_sesion.php";  // Exige sesión activa antes de procesar

// Este controlador ahora exige el permiso "categorias"
if (!tienePermiso("categorias")) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permiso+para+esta+acción");
    exit();
}

// ===========================
// guardar_categoria.php — CONTROLLER
// Programación 4: Aplicaciones Web — UAE
// ===========================

require_once "../model/CategoriaModel.php";

$nombre      = trim($_POST["nombre"]      ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

if (empty($nombre)) {
    header("Location: ../view/categorias.php?msg=error&detalle=El+nombre+es+obligatorio");
    exit();
}

try {
    $modelo = new CategoriaModel();
    $modelo->guardar($nombre, $descripcion);

    header("Location: ../view/categorias.php?msg=ok");
    exit();

} catch (PDOException $e) {
    header("Location: ../view/categorias.php?msg=error&detalle=" . urlencode($e->getMessage()));
    exit();
}
?>
