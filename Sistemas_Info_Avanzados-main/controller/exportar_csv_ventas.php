<?php
require_once "../model/verificar_sesion.php";
require_once "../model/VentaModel.php";

$modelo = new VentaModel();

$fechaInicio = $_GET["fecha_inicio"] ?? "";
$fechaFin    = $_GET["fecha_fin"]    ?? "";

if (!empty($fechaInicio) && !empty($fechaFin)) {
    $ventas = $modelo->obtenerPorRangoFechas($fechaInicio, $fechaFin);
} else {
    $ventas = $modelo->obtenerTodas();
}

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=ventas.csv");

$salida = fopen("php://output", "w");
fputcsv($salida, ["ID", "Cliente", "Producto", "Cantidad", "Precio Unit.", "Total", "Vendedor", "Fecha"]);

foreach ($ventas as $v) {
    fputcsv($salida, [$v["id"], $v["cliente"] . " " . $v["cliente_apellido"], $v["producto"], $v["cantidad"], $v["precio_unitario"], $v["total"], $v["usuario"], $v["fecha_venta"]]);
}
fclose($salida);
exit();
?>
