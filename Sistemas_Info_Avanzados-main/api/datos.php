<?php
// ===========================
// api/datos.php — ENDPOINT DE DATOS (JSON / CSV)
// Sistemas de Información Avanzada — UAE
// ===========================
// Entrega los datos del sistema para que otras herramientas los lean:
// Power BI Service, Looker Studio (vía Google Sheets), Postman, QGIS...
//
// Uso:
//   api/datos.php?token=SU_TOKEN                          → ventas en JSON
//   api/datos.php?token=SU_TOKEN&recurso=productos        → productos en JSON
//   api/datos.php?token=SU_TOKEN&recurso=ventas&formato=csv → ventas en CSV
//
// Recursos disponibles: ventas, compras, productos
// Por la LOPDP, el endpoint NO entrega datos personales de clientes
// (cédula, teléfono, email).

require_once __DIR__ . "/../model/Conexion.php";   // también carga config.php

ini_set("serialize_precision", "-1");   // evita decimales largos (9.0999999...) en el JSON
header("Access-Control-Allow-Origin: *");

// ── 1. Seguridad: token ──
if (($_GET["token"] ?? "") !== API_TOKEN) {
    header("Content-Type: application/json; charset=utf-8");
    http_response_code(401);
    echo json_encode(["error" => "Token inválido o ausente"]);
    exit;
}

// ── 2. Consultas disponibles (solo lectura, sin datos del usuario) ──
$consultas = [
    "ventas" => "SELECT v.id, DATE(v.fecha_venta) AS fecha, p.nombre AS producto, c.nombre AS categoria,
                        u.nombre AS vendedor, v.id_cliente, v.cantidad, v.precio_unitario, v.total
                 FROM ventas v
                 JOIN productos  p ON p.id = v.id_producto
                 JOIN categorias c ON c.id = p.id_categoria
                 JOIN usuarios   u ON u.id = v.id_usuario
                 ORDER BY v.fecha_venta",
    "compras" => "SELECT co.id, DATE(co.fecha_compra) AS fecha, p.nombre AS producto, c.nombre AS categoria,
                         pr.nombre_comercial AS proveedor, co.cantidad, co.precio_unitario, co.total
                  FROM compras co
                  JOIN productos   p  ON p.id  = co.id_producto
                  JOIN categorias  c  ON c.id  = p.id_categoria
                  JOIN proveedores pr ON pr.id = co.id_proveedor
                  ORDER BY co.fecha_compra",
    "productos" => "SELECT p.id, p.nombre AS producto, c.nombre AS categoria, p.unidad, p.precio, p.stock
                    FROM productos p
                    JOIN categorias c ON c.id = p.id_categoria
                    ORDER BY p.nombre",
];
// Columnas numéricas: se convierten para que Power BI y Looker las reconozcan como números
$enteros   = ["id", "id_cliente", "cantidad", "stock"];
$decimales = ["precio_unitario", "total", "precio"];

$recurso = $_GET["recurso"] ?? "ventas";
if (!isset($consultas[$recurso])) {
    header("Content-Type: application/json; charset=utf-8");
    http_response_code(404);
    echo json_encode(["error" => "Recurso no válido", "disponibles" => array_keys($consultas)]);
    exit;
}

try {
    $pdo   = (new Conexion())->getConexion();
    $filas = $pdo->query($consultas[$recurso])->fetchAll(PDO::FETCH_ASSOC);

    foreach ($filas as &$fila) {
        foreach ($enteros as $campo)   { if (isset($fila[$campo])) $fila[$campo] = (int) $fila[$campo]; }
        foreach ($decimales as $campo) { if (isset($fila[$campo])) $fila[$campo] = round((float) $fila[$campo], 2); }
    }
    unset($fila);   // IMPORTANTE: sin esto, el siguiente foreach sobrescribe la última fila

    // ── 3a. Salida CSV (Google Sheets: =IMPORTDATA("URL&formato=csv")) ──
    if (($_GET["formato"] ?? "json") === "csv") {
        header("Content-Type: text/csv; charset=utf-8");
        $salida = fopen("php://output", "w");
        if (!empty($filas)) {
            fputcsv($salida, array_keys($filas[0]));
            foreach ($filas as $fila) { fputcsv($salida, $fila); }
        }
        fclose($salida);
        exit;
    }

    // ── 3b. Salida JSON (Power BI, Postman, JavaScript) ──
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "recurso"   => $recurso,
        "generado"  => date("Y-m-d H:i:s"),
        "registros" => count($filas),
        "datos"     => $filas,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    header("Content-Type: application/json; charset=utf-8");
    http_response_code(500);
    echo json_encode(["error" => "Error al consultar la base de datos"]);
}
