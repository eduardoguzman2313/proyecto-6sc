<?php
// ===========================
// VentaModel.php — MODEL (POO)
// Programación 4: Aplicaciones Web — UAE
// ===========================
// Igual que las compras, pero al revés: aquí el stock DISMINUYE.
// Además hay una validación extra muy importante: no se puede
// vender más de lo que hay en stock.

require_once "Conexion.php";

class VentaModel {

    private $pdo;

    public function __construct() {
        $conexion  = new Conexion();
        $this->pdo = $conexion->getConexion();
    }

    // Buscar un producto por su ID — lo usamos para saber su precio
    // y su stock disponible ANTES de vender
    public function obtenerProducto($id_producto) {
        $sql  = "SELECT * FROM productos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":id" => $id_producto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Registrar una venta completa: verificar stock + guardar + disminuir stock + historial
    public function registrarVenta($id_producto, $cantidad, $id_usuario, $id_cliente) {

        $producto = $this->obtenerProducto($id_producto);

        // Si el producto no existe, no hay nada que vender
        if (!$producto) {
            throw new Exception("El producto no existe.");
        }

        // Validación clave: no se puede vender más de lo que hay
        if ($producto["stock"] < $cantidad) {
            throw new Exception("Stock insuficiente. Disponible: " . $producto["stock"]);
        }

        // El precio se toma del producto (no lo escribe el empleado a mano)
        $precio_unitario = $producto["precio"];
        $total           = $cantidad * $precio_unitario;

        try {
            $this->pdo->beginTransaction();

            // 1) Insertar la venta — ahora también con el cliente
            $sqlVenta = "INSERT INTO ventas (id_cliente, id_producto, id_usuario, cantidad, precio_unitario, total)
                         VALUES (:id_cliente, :id_producto, :id_usuario, :cantidad, :precio_unitario, :total)";
            $stmtVenta = $this->pdo->prepare($sqlVenta);
            $stmtVenta->execute([
                ":id_cliente"      => $id_cliente,
                ":id_producto"     => $id_producto,
                ":id_usuario"      => $id_usuario,
                ":cantidad"        => $cantidad,
                ":precio_unitario" => $precio_unitario,
                ":total"           => $total
            ]);

            $idVentaGenerada = $this->pdo->lastInsertId();

            // 2) Disminuir el stock del producto vendido
            $sqlStock = "UPDATE productos
                         SET stock = stock - :cantidad
                         WHERE id = :id_producto";
            $stmtStock = $this->pdo->prepare($sqlStock);
            $stmtStock->execute([
                ":cantidad"    => $cantidad,
                ":id_producto" => $id_producto
            ]);

            // 3) Historial de movimientos — esta vez tipo 'salida'
            $sqlMovimiento = "INSERT INTO movimientos_stock (id_producto, tipo, cantidad, referencia)
                              VALUES (:id_producto, 'salida', :cantidad, :referencia)";
            $stmtMovimiento = $this->pdo->prepare($sqlMovimiento);
            $stmtMovimiento->execute([
                ":id_producto" => $id_producto,
                ":cantidad"    => $cantidad,
                ":referencia"  => "Venta #" . $idVentaGenerada
            ]);

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function obtenerTodas() {
        $sql = "SELECT v.*,
                       c.nombre   AS cliente,
                       c.apellido AS cliente_apellido,
                       p.nombre   AS producto,
                       u.nombre   AS usuario
                FROM ventas v
                INNER JOIN clientes  c ON v.id_cliente  = c.id
                INNER JOIN productos p ON v.id_producto = p.id
                INNER JOIN usuarios  u ON v.id_usuario   = u.id
                ORDER BY v.fecha_venta DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Filtrar ventas por rango de fechas ──
    public function obtenerPorRangoFechas($fechaInicio, $fechaFin) {
        $sql = "SELECT v.*,
                       c.nombre   AS cliente,
                       c.apellido AS cliente_apellido,
                       p.nombre   AS producto,
                       u.nombre   AS usuario
                FROM ventas v
                INNER JOIN clientes  c ON v.id_cliente  = c.id
                INNER JOIN productos p ON v.id_producto = p.id
                INNER JOIN usuarios  u ON v.id_usuario   = u.id
                WHERE DATE(v.fecha_venta) >= :inicio
                  AND DATE(v.fecha_venta) <= :fin
                ORDER BY v.fecha_venta DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":inicio" => $fechaInicio,
            ":fin"    => $fechaFin
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
