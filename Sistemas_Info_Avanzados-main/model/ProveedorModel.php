<?php
// ===========================
// ProveedorModel.php — MODEL (POO)
// Programación 4: Aplicaciones Web — UAE
// ===========================
// Mismo patrón que CategoriaModel y ClienteModel — "otro cocinero"
// del restaurante, especializado en proveedores.

require_once "Conexion.php";

class ProveedorModel {

    private $pdo;

    public function __construct() {
        $conexion  = new Conexion();
        $this->pdo = $conexion->getConexion();
    }

    public function guardar($nombre_comercial, $nombre, $telefono, $email, $direccion) {
        $sql  = "INSERT INTO proveedores (nombre_comercial, nombre, telefono, email, direccion)
                 VALUES (:nombre_comercial, :nombre, :telefono, :email, :direccion)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":nombre_comercial" => $nombre_comercial,
            ":nombre"           => $nombre,
            ":telefono"         => $telefono,
            ":email"            => $email,
            ":direccion"        => $direccion
        ]);
        return true;
    }

    public function obtenerTodos() {
        $stmt = $this->pdo->query("SELECT * FROM proveedores ORDER BY nombre_comercial ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
