<?php
// ===========================
// ProductoModel.php — MODEL (POO)
// Programación 4: Aplicaciones Web — UAE
// ===========================

require_once "Conexion.php";

class ProductoModel {

    private $pdo;

    public function __construct() {
        $conexion  = new Conexion();
        $this->pdo = $conexion->getConexion();
    }

    // Insertar nuevo producto
    public function guardar($nombre, $id_categoria, $descripcion, $precio, $unidad) {
        $sql  = "INSERT INTO productos (nombre, id_categoria, descripcion, precio, unidad)
                 VALUES (:nombre, :id_categoria, :descripcion, :precio, :unidad)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":nombre"       => $nombre,
            ":id_categoria" => $id_categoria,
            ":descripcion"  => $descripcion,
            ":precio"       => $precio,
            ":unidad"       => $unidad
        ]);
        return true;
    }

    // Obtener todos los productos con el nombre de su categoría (JOIN)
    public function obtenerTodos() {
        $sql  = "SELECT p.*, c.nombre AS categoria
                 FROM productos p
                 INNER JOIN categorias c ON p.id_categoria = c.id
                 ORDER BY p.fecha_registro DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
