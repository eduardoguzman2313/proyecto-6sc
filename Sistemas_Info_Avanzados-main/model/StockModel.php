<?php
// ===========================
// StockModel.php — MODEL (POO)
// Programación 4: Aplicaciones Web — UAE
// ===========================
// Este cocinero solo CONSULTA — no guarda nada nuevo.
// Sirve para el historial de movimientos y para el gráfico
// de productos más vendidos que se usará en el Dashboard.

require_once "Conexion.php";

class StockModel {

    private $pdo;

    public function __construct() {
        $conexion  = new Conexion();
        $this->pdo = $conexion->getConexion();
    }

    // Historial completo de entradas y salidas de stock
    public function obtenerHistorial() {
        $sql = "SELECT m.*, p.nombre AS producto
                FROM movimientos_stock m
                INNER JOIN productos p ON m.id_producto = p.id
                ORDER BY m.fecha_movimiento DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Stock actual de todos los productos (para el PDF/CSV de stock)
    public function obtenerStockActual() {
        $stmt = $this->pdo->query("SELECT id, nombre, stock, precio FROM productos ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Top 5 productos más vendidos (para el gráfico del Dashboard)
    public function obtenerMasVendidos() {
        $sql = "SELECT p.nombre, SUM(v.cantidad) AS total_vendido
                FROM ventas v
                INNER JOIN productos p ON v.id_producto = p.id
                GROUP BY p.id, p.nombre
                ORDER BY total_vendido DESC
                LIMIT 5";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Kardex de un producto — el "libro contable" del inventario ──
    // Trae los movimientos de UN producto, ordenados del más viejo al
    // más nuevo, para poder calcular el saldo acumulado paso a paso.
    public function obtenerMovimientosPorProducto($id_producto) {
        $sql = "SELECT m.*, p.nombre AS producto
                FROM movimientos_stock m
                INNER JOIN productos p ON m.id_producto = p.id
                WHERE m.id_producto = :id_producto
                ORDER BY m.fecha_movimiento ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":id_producto" => $id_producto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
