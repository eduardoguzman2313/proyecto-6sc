<?php
require_once "../model/verificar_sesion.php";
require_once "../model/ProductoModel.php";

$modelo    = new ProductoModel();
$productos = $modelo->obtenerTodos();

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=productos.csv");

$salida = fopen("php://output", "w");
fputcsv($salida, ["ID", "Nombre", "Categoría", "Descripción", "Precio", "Unidad", "Stock", "Fecha Registro"]);

foreach ($productos as $p) {
    fputcsv($salida, [$p["id"], $p["nombre"], $p["categoria"], $p["descripcion"], $p["precio"], $p["unidad"], $p["stock"] ?? 0, $p["fecha_registro"]]);
}
fclose($salida);
exit();
?>
