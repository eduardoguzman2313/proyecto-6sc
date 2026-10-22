<?php
// ===========================
// procesar_login.php — CONTROLLER
// Programación 4: Aplicaciones Web — UAE
// ===========================
// Es el "mesero" que recibe el intento de acceso, se lo pregunta
// al cocinero (UsuarioModel) y decide a dónde mandar al usuario.

session_start();
require_once "../model/UsuarioModel.php";

// 1. Leer lo que el usuario escribió en el formulario
$email    = trim($_POST["email"]    ?? "");
$password = trim($_POST["password"] ?? "");

// 2. Revisar que no venga vacío
if (empty($email) || empty($password)) {
    header("Location: ../view/login.php?msg=error&detalle=Complete+todos+los+campos");
    exit();
}

// 3. Preguntarle al modelo si esas credenciales son correctas
$modelo  = new UsuarioModel();
$usuario = $modelo->verificarCredenciales($email, $password);

if ($usuario) {

    // Antes de dejarlo entrar: ¿su cuenta está activa?
    if ($usuario["estado"] !== "activo") {
        header("Location: ../view/login.php?msg=error&detalle=Su+cuenta+está+inactiva.+Contacte+al+administrador");
        exit();
    }

    // Credenciales correctas: abrimos la sesión (la "mochila" del usuario)
    $_SESSION["usuario_id"]       = $usuario["id"];
    $_SESSION["nombre"]           = $usuario["nombre"];
    $_SESSION["rol"]              = $usuario["rol"];       // solo una etiqueta (ej: "Bodeguero")
    $_SESSION["es_admin"]         = (bool) $usuario["es_admin"];

    // permisos se guarda en la BD como texto separado por comas
    // (ej: "compras,kardex") — aquí lo convertimos a un arreglo real de PHP
    $_SESSION["permisos"]         = !empty($usuario["permisos"])
                                     ? explode(",", $usuario["permisos"])
                                     : [];

    $_SESSION["ultima_actividad"] = time();

    // Mandamos al usuario al panel principal
    header("Location: ../index.php");
    exit();

} else {
    // Credenciales incorrectas: regresamos al login con un mensaje
    header("Location: ../view/login.php?msg=error&detalle=Email+o+contraseña+incorrectos");
    exit();
}
?>
