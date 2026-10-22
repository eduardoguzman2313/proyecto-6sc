<?php
// ===========================
// ClienteModel.php — MODEL (POO)
// Programación 4: Aplicaciones Web — UAE
// ===========================
// Esta clase se encarga de TODO lo relacionado con la tabla clientes.
// El controlador NO escribe SQL — le pide a este modelo que lo haga.

require_once "Conexion.php";

class ClienteModel {

    // Atributo privado: la conexión PDO
    private $pdo;

    // Constructor: al crear el objeto, ya se conecta
    public function __construct() {
        $conexion    = new Conexion();
        $this->pdo   = $conexion->getConexion();
    }

    // Método: insertar un nuevo cliente
    public function guardar($nombre, $apellido, $cedula, $telefono, $email) {
        $sql  = "INSERT INTO clientes (nombre, apellido, cedula, telefono, email)
                 VALUES (:nombre, :apellido, :cedula, :telefono, :email)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":nombre"   => $nombre,
            ":apellido" => $apellido,
            ":cedula"   => $cedula,
            ":telefono" => $telefono,
            ":email"    => $email
        ]);
        return true;
    }

    // Método: obtener todos los clientes
    public function obtenerTodos() {
        $stmt = $this->pdo->query("SELECT * FROM clientes ORDER BY fecha_registro DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
