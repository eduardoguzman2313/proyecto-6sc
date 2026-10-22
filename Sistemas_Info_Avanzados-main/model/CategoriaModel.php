<?php
// ===========================
// CategoriaModel.php — MODEL (POO)
// Programación 4: Aplicaciones Web — UAE
// ===========================

require_once "Conexion.php";

class CategoriaModel {

    private $pdo;

    public function __construct() {
        $conexion  = new Conexion();
        $this->pdo = $conexion->getConexion();
    }

    // Insertar nueva categoría
    public function guardar($nombre, $descripcion) {
        $sql  = "INSERT INTO categorias (nombre, descripcion)
                 VALUES (:nombre, :descripcion)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":nombre"      => $nombre,
            ":descripcion" => $descripcion
        ]);
        return true;
    }

    // Obtener todas las categorías (se usa en el formulario de productos)
    public function obtenerTodas() {
        $stmt = $this->pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
