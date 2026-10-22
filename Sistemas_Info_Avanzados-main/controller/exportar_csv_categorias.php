<?php
require_once "../model/verificar_sesion.php";
require_once "../model/CategoriaModel.php";

$modelo     = new CategoriaModel();
$categorias = $modelo->obtenerTodas();

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=categorias.csv");

$salida = fopen("php://output", "w");
fputcsv($salida, ["ID", "Nombre", "Descripción", "Fecha Registro"]);

foreach ($categorias as $c) {
    fputcsv($salida, [$c["id"], $c["nombre"], $c["descripcion"], $c["fecha_registro"]]);
}
fclose($salida);
exit();
?>
