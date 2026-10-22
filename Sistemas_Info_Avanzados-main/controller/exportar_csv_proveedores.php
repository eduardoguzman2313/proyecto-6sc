<?php
require_once "../model/verificar_sesion.php";
require_once "../model/ProveedorModel.php";

$modelo      = new ProveedorModel();
$proveedores = $modelo->obtenerTodos();

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=proveedores.csv");

$salida = fopen("php://output", "w");
fputcsv($salida, ["ID", "Nombre Comercial", "Contacto", "Teléfono", "Email", "Dirección", "Fecha Registro"]);

foreach ($proveedores as $p) {
    fputcsv($salida, [$p["id"], $p["nombre_comercial"], $p["nombre"], $p["telefono"], $p["email"], $p["direccion"], $p["fecha_registro"]]);
}
fclose($salida);
exit();
?>
