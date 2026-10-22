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

$fechaHoy = date("d/m/Y H:i");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Reporte de Compras</title>
    <style>body { font-family: Arial, sans-serif; margin: 40px; color: #2D2D2D; }
        h1 { color: #1F7A8C; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; font-size: 13px; }
        th { background-color: #1F7A8C; color: white; }
        .fecha { color: #555; margin-bottom: 20px; }
        @media print { .btn-imprimir { display: none; } }</style>
</head>
<body>
    <button class="btn-imprimir" onclick="window.print()" style="padding:10px 20px; margin-bottom:20px;">🖨️ Descargar / Imprimir PDF</button>
    <h1>Reporte de Compras — Mi Proyecto</h1>
    <p class="fecha">
        Generado el: <?= $fechaHoy ?> — Total: <?= count($compras) ?> compras
        <?php if (!empty($fechaInicio) && !empty($fechaFin)): ?>
            (Filtrado: <?= $fechaInicio ?> al <?= $fechaFin ?>)
        <?php endif; ?>
    </p>
    <table>
        <thead><tr><th>#</th><th>Proveedor</th><th>Producto</th><th>Cant.</th><th>P. Costo</th><th>Margen</th><th>P. Venta</th><th>Total</th><th>Registrado por</th><th>Fecha</th></tr></thead>
        <tbody>
            <?php foreach ($compras as $c): ?>
                <tr>
                    <td><?= $c["id"] ?></td>
                    <td><?= htmlspecialchars($c["proveedor"]) ?></td>
                    <td><?= htmlspecialchars($c["producto"]) ?></td>
                    <td><?= $c["cantidad"] ?></td>
                    <td>$<?= number_format($c["precio_unitario"], 2) ?></td>
                    <td><?= $c["margen_ganancia"] ?>%</td>
                    <td>$<?= number_format($c["precio_venta"], 2) ?></td>
                    <td>$<?= number_format($c["total"], 2) ?></td>
                    <td><?= htmlspecialchars($c["usuario"]) ?></td>
                    <td><?= $c["fecha_compra"] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
