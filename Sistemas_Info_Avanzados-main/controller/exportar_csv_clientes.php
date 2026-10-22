<?php
require_once "../model/verificar_sesion.php";
require_once "../model/ClienteModel.php";

$modelo   = new ClienteModel();
$clientes = $modelo->obtenerTodos();

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=clientes.csv");

$salida = fopen("php://output", "w");
fputcsv($salida, ["ID", "Nombre", "Apellido", "Cédula", "Teléfono", "Email", "Fecha Registro"]);

foreach ($clientes as $c) {
    fputcsv($salida, [$c["id"], $c["nombre"], $c["apellido"], $c["cedula"], $c["telefono"], $c["email"], $c["fecha_registro"]]);
}
fclose($salida);
exit();
?>
