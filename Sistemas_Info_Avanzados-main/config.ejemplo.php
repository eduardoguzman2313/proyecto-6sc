<?php
// ===========================
// config.ejemplo.php — CONFIGURACIÓN
// Sistemas de Información Avanzada — UAE
// ===========================
// 1. Copie este archivo y renómbrelo como  config.php
// 2. Escriba los datos de SU base de datos (XAMPP o hosting).
// 3. config.php NUNCA se sube a GitHub (está en .gitignore),
//    porque contiene contraseñas. Este archivo de ejemplo sí se sube.

// ── Base de datos ──
define("DB_HOST", "localhost");      // En AwardSpace: el host que indica el panel (ej: fdb1234.awardspace.net)
define("DB_NAME", "sia_base");       // Nombre de la base de datos
define("DB_USER", "root");           // XAMPP: root  |  Hosting: el usuario que creó
define("DB_PASS", "");               // XAMPP: vacío |  Hosting: su contraseña

// ── API de datos (api/datos.php) ──
// Cambie este token por uno propio. Quien no lo conozca no podrá leer los datos.
define("API_TOKEN", "cambie-este-token");

// ── Zona horaria de Ecuador ──
date_default_timezone_set("America/Guayaquil");
