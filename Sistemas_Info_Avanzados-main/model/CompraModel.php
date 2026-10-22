<?php
// ===========================
// CompraModel.php — MODEL (POO)
// Programación 4: Aplicaciones Web — UAE
// ===========================
// Este cocinero es más complejo: cuando se registra una compra,
// pasan TRES cosas a la vez:
//   1) se guarda la compra
//   2) el stock del producto AUMENTA
//   3) queda una huella en el historial de movimientos
//
// ¿Y si el internet falla justo después del paso 1? Tendríamos una
// compra guardada pero el stock NUNCA aumentó — un dato inconsistente.
// Para evitar eso usamos una TRANSACCIÓN: las 3 acciones se hacen
// como si fueran una sola. O se completan las 3, o no se completa
// ninguna (rollback).

require_once "Conexion.php";

class CompraModel {

    private $pdo;

    public function __construct() {
        $conexion  = new Conexion();
        $this->pdo = $conexion->getConexion();
    }

    // Registrar una compra completa: guardar + aumentar stock + ACTUALIZAR
    // el precio de venta según el margen + historial
    public function registrarCompra($id_proveedor, $id_producto, $cantidad, $precio_unitario, $id_usuario, $margen_ganancia) {

        // El total de la COMPRA (lo que se le paga al proveedor)
        $total = $cantidad * $precio_unitario;

        // El precio de VENTA se calcula sumándole el margen de ganancia
        // al precio de costo. Ejemplo: costo $7, margen 10%
        //   precio_venta = 7 * (1 + 10/100) = 7 * 1.10 = 7.70
        $precio_venta = $precio_unitario * (1 + ($margen_ganancia / 100));

        try {
            // beginTransaction() — "empieza el paquete de 4 acciones"
            $this->pdo->beginTransaction();

            // 1) Insertar la compra (guardamos también el margen usado
            //    y el precio de venta resultante, para tener el historial)
            $sqlCompra = "INSERT INTO compras
                          (id_proveedor, id_producto, id_usuario, cantidad, precio_unitario, margen_ganancia, precio_venta, total)
                          VALUES (:id_proveedor, :id_producto, :id_usuario, :cantidad, :precio_unitario, :margen_ganancia, :precio_venta, :total)";
            $stmtCompra = $this->pdo->prepare($sqlCompra);
            $stmtCompra->execute([
                ":id_proveedor"     => $id_proveedor,
                ":id_producto"      => $id_producto,
                ":id_usuario"       => $id_usuario,
                ":cantidad"         => $cantidad,
                ":precio_unitario"  => $precio_unitario,
                ":margen_ganancia"  => $margen_ganancia,
                ":precio_venta"     => $precio_venta,
                ":total"            => $total
            ]);

            $idCompraGenerada = $this->pdo->lastInsertId();

            // 2) Aumentar el stock Y actualizar el precio de venta del producto
            //    Así, la próxima venta ya sale con el precio correcto —
            //    nunca al mismo precio que se compró.
            $sqlStock = "UPDATE productos
                         SET stock = stock + :cantidad,
                             precio = :precio_venta
                         WHERE id = :id_producto";
            $stmtStock = $this->pdo->prepare($sqlStock);
            $stmtStock->execute([
                ":cantidad"     => $cantidad,
                ":precio_venta" => $precio_venta,
                ":id_producto"  => $id_producto
            ]);

            // 3) Dejar la huella en el historial de movimientos
            $sqlMovimiento = "INSERT INTO movimientos_stock (id_producto, tipo, cantidad, referencia)
                              VALUES (:id_producto, 'entrada', :cantidad, :referencia)";
            $stmtMovimiento = $this->pdo->prepare($sqlMovimiento);
            $stmtMovimiento->execute([
                ":id_producto" => $id_producto,
                ":cantidad"    => $cantidad,
                ":referencia"  => "Compra #" . $idCompraGenerada
            ]);

            // Si las 3 acciones salieron bien, confirmamos todo junto
            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            // Si algo falló, deshacemos TODO — como si nada hubiera pasado
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Traer todas las compras con el nombre del proveedor, producto y usuario
    public function obtenerTodas() {
        $sql = "SELECT c.*,
                       pr.nombre_comercial AS proveedor,
                       prod.nombre AS producto,
                       u.nombre   AS usuario
                FROM compras c
                INNER JOIN proveedores pr  ON c.id_proveedor = pr.id
                INNER JOIN productos   prod ON c.id_producto  = prod.id
                INNER JOIN usuarios    u    ON c.id_usuario    = u.id
                ORDER BY c.fecha_compra DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Filtrar compras por rango de fechas ──
    // Si $fechaInicio o $fechaFin vienen vacíos, no se filtra por ese lado.
    public function obtenerPorRangoFechas($fechaInicio, $fechaFin) {
        $sql = "SELECT c.*,
                       pr.nombre_comercial AS proveedor,
                       prod.nombre AS producto,
                       u.nombre   AS usuario
                FROM compras c
                INNER JOIN proveedores pr  ON c.id_proveedor = pr.id
                INNER JOIN productos   prod ON c.id_producto  = prod.id
                INNER JOIN usuarios    u    ON c.id_usuario    = u.id
                WHERE DATE(c.fecha_compra) >= :inicio
                  AND DATE(c.fecha_compra) <= :fin
                ORDER BY c.fecha_compra DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":inicio" => $fechaInicio,
            ":fin"    => $fechaFin
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
