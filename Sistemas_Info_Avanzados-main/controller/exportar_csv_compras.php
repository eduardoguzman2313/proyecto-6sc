<?php
require_once "../model/verificar_sesion.php";
require_once "../model/CompraModel.php";

$modelo = new CompraModel();

$fechaInicio = $_GET["fecha_inicio"] ?? "";
$fechaFin    = $_GET["fecha_fin"]    ?? "";

if (!empty($fechaInicio) && !empty($fechaFin)) {
    $compras = $modelo->obtenerPorRangoFechas($fechaInicio, $fechaFin);
} else {
    $compras = $modelo->obtenerTodas();
}

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=compras.csv");

$salida = fopen("php://output", "w");
fputcsv($salida, ["ID", "Proveedor", "Producto", "Cantidad", "Precio Costo", "Margen %", "Precio Venta", "Total", "Registrado por", "Fecha"]);

foreach ($compras as $c) {
    fputcsv($salida, [$c["id"], $c["proveedor"], $c["producto"], $c["cantidad"], $c["precio_unitario"], $c["margen_ganancia"], $c["precio_venta"], $c["total"], $c["usuario"], $c["fecha_compra"]]);
}
fclose($salida);
exit();
?>
