<?php
require_once "../model/verificar_sesion.php";  // Exige sesión activa antes de procesar

// Este controlador ahora exige el permiso "clientes"
if (!tienePermiso("clientes")) {
    header("Location: ../index.php?msg=error&detalle=No+tiene+permiso+para+esta+acción");
    exit();
}

// ===========================
// guardar_cliente.php — CONTROLLER
// Programación 4: Aplicaciones Web — UAE
// ===========================
// Recibe el POST del formulario, valida, y usa el MODEL (POO) para guardar.

require_once "../model/ClienteModel.php";

// 1. Leer datos del formulario
$nombre   = trim($_POST["nombre"]   ?? "");
$apellido = trim($_POST["apellido"] ?? "");
$cedula   = trim($_POST["cedula"]   ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$email    = trim($_POST["email"]    ?? "");

// 2. Validar que ningún campo esté vacío
if (empty($nombre) || empty($apellido) || empty($cedula) || empty($telefono) || empty($email)) {
    header("Location: ../view/clientes.php?msg=error&detalle=Todos+los+campos+son+obligatorios");
    exit();
}

// 3. Usar el MODEL (POO) para guardar — el controlador NO escribe SQL
try {
    $modelo = new ClienteModel();
    $modelo->guardar($nombre, $apellido, $cedula, $telefono, $email);

    header("Location: ../view/clientes.php?msg=ok");
    exit();

} catch (PDOException $e) {
    header("Location: ../view/clientes.php?msg=error&detalle=" . urlencode($e->getMessage()));
    exit();
}
?>
