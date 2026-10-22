<?php
// ===========================
// logout.php — CONTROLLER
// Programación 4: Aplicaciones Web — UAE
// ===========================
// Cierra la sesión manualmente cuando el usuario hace clic en
// "Cerrar sesión" — es la salida voluntaria, distinta al cierre
// automático por inactividad de verificar_sesion.php

session_start();

// Vaciar todos los datos guardados en la sesión
session_unset();

// Destruir la sesión por completo
session_destroy();

// Regresar al login con un mensaje de confirmación
header("Location: ../view/login.php?msg=logout");
exit();
?>
