<?php
// CONTROLLER — exportar_csv_stock.php
// Programación 4: Aplicaciones Web — UAE
// ===========================
require_once "../model/verificar_sesion.php";
require_once "../model/StockModel.php";

$modelo      = new StockModel();
$stockActual = $modelo->obtenerStockActual();

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=stock_actual.csv");

$salida = fopen("php://output", "w");

fputcsv($salida, ["ID", "Producto", "Stock", "Precio"]);

foreach ($stockActual as $p) {
    fputcsv($salida, [
        $p["id"],
        $p["nombre"],
        $p["stock"],
        $p["precio"],
    ]);
}

fclose($salida);
exit();
?>
