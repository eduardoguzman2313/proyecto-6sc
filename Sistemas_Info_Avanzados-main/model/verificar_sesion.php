<?php
// ===========================
// verificar_sesion.php — MODEL (helper de seguridad)
// Programación 4: Aplicaciones Web — UAE
// ===========================
// Este archivo se incluye al INICIO de cada vista protegida
// (clientes.php, categorias.php, productos.php, etc.)
// Hace dos cosas: 1) revisa si la sesión caducó por inactividad,
//                 2) revisa si el usuario inició sesión.

// session_start() es abrir la  ($_SESSION) de este usuario.
// Debe ir SIEMPRE al principio del archivo, antes de MOSTRAR cualquier HTML.
session_start();

// Tiempo máximo permitido sin actividad, en segundos.
// 900 segundos = 15 minutos. Si pasa más tiempo sin recargar
// ninguna página, la sesión se cierra automáticamente.
$tiempoMaximoInactividad = 900;

// ¿Ya existe una marca de "última actividad" guardada?
// Si existe, comparamos cuánto tiempo ha pasado desde entonces.
if (isset($_SESSION["ultima_actividad"])) {
    $segundosInactivo = time() - $_SESSION["ultima_actividad"];

    if ($segundosInactivo > $tiempoMaximoInactividad) {
        // Se acabó el tiempo: destruimos la sesión completa
        session_unset();
        session_destroy();

        header("Location: login.php?msg=inactividad");
        exit();
    }
}

// ¿El usuario nunca inició sesión (o ya se cerró)?
// $_SESSION["usuario_id"] solo existe si el login fue exitoso.
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php?msg=no_autenticado");
    exit();
}

// Si llegamos hasta aquí, el usuario está activo y autenticado.
// Actualizamos la marca de tiempo para reiniciar el conteo de inactividad.
$_SESSION["ultima_actividad"] = time();

// Cargamos la función tienePermiso() — disponible automáticamente
// en cualquier archivo que ya incluya verificar_sesion.php
require_once __DIR__ . "/permisos.php";
?>
