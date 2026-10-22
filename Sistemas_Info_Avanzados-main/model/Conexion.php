<?php
// ===========================
// Conexion.php — MODEL (POO)
// Sistemas de Información Avanzada — UAE
// ===========================
// Clase que encapsula la conexión PDO.
// Los datos de acceso ya NO se escriben aquí: se leen de config.php,
// que no se sube a GitHub. Así el código puede compartirse sin exponer
// la contraseña de la base de datos.

$rutaConfig = __DIR__ . "/../config.php";
if (!file_exists($rutaConfig)) {
    die("❌ Falta el archivo config.php. Copie config.ejemplo.php como config.php y escriba los datos de su base de datos.");
}
require_once $rutaConfig;

class Conexion {

    // Atributos PRIVADOS — solo esta clase los conoce
    private $host     = DB_HOST;
    private $dbname   = DB_NAME;
    private $usuario  = DB_USER;
    private $password = DB_PASS;
    private $pdo      = null;

    // Método PÚBLICO — la única puerta de entrada a la conexión
    public function getConexion() {
        // Si ya existe una conexión abierta, la reutiliza (no abre 100 conexiones)
        if ($this->pdo === null) {
            try {
                $this->pdo = new PDO(
                    "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                    $this->usuario,
                    $this->password
                );
                // Activa el modo excepción para detectar errores SQL
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // Hora de Ecuador para NOW() y current_timestamp (el hosting suele estar en UTC)
                $this->pdo->exec("SET time_zone = '-05:00'");

            } catch (PDOException $e) {
                // No mostramos el detalle técnico al público (puede revelar datos del servidor)
                die("❌ Error de conexión con la base de datos. Revise los datos de config.php.");
            }
        }
        return $this->pdo;
    }
}
?>
