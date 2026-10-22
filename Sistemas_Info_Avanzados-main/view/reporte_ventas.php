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

$fechaHoy = date("d/m/Y H:i");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Reporte de Ventas</title>
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
    <h1>Reporte de Ventas — Mi Proyecto</h1>
    <p class="fecha">
        Generado el: <?= $fechaHoy ?> — Total: <?= count($ventas) ?> ventas
        <?php if (!empty($fechaInicio) && !empty($fechaFin)): ?>
            (Filtrado: <?= $fechaInicio ?> al <?= $fechaFin ?>)
        <?php endif; ?>
    </p>
    <table>
        <thead><tr><th>#</th><th>Cliente</th><th>Producto</th><th>Cant.</th><th>P. Unit.</th><th>Total</th><th>Vendedor</th><th>Fecha</th></tr></thead>
        <tbody>
            <?php foreach ($ventas as $v): ?>
                <tr>
                    <td><?= $v["id"] ?></td>
                    <td><?= htmlspecialchars($v["cliente"] . " " . $v["cliente_apellido"]) ?></td>
                    <td><?= htmlspecialchars($v["producto"]) ?></td>
                    <td><?= $v["cantidad"] ?></td>
                    <td>$<?= number_format($v["precio_unitario"], 2) ?></td>
                    <td>$<?= number_format($v["total"], 2) ?></td>
                    <td><?= htmlspecialchars($v["usuario"]) ?></td>
                    <td><?= $v["fecha_venta"] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
