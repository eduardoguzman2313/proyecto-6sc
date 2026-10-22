<?php
// VIEW — reporte_stock.php
// Programación 4: Aplicaciones Web — UAE
// ===========================
// Esta página está diseñada para IMPRIMIRSE. El botón "Descargar PDF"
// abre el diálogo de impresión del navegador — ahí el usuario elige
// "Guardar como PDF" en vez de una impresora física. Así generamos
// un PDF real sin necesitar ninguna librería externa de PHP.

require_once "../model/verificar_sesion.php";
require_once "../model/StockModel.php";

$modelo      = new StockModel();
$stockActual = $modelo->obtenerStockActual();
$fechaHoy    = date("d/m/Y H:i");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Reporte de Stock</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #2D2D2D; }
        h1 { color: #1F7A8C; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background-color: #1F7A8C; color: white; }
        .fecha { color: #555; margin-bottom: 20px; }

        /* Esta regla SOLO aplica cuando se imprime o se guarda como PDF.
           Oculta el botón — no tiene sentido que aparezca en el PDF final. */
        @media print {
            .btn-imprimir { display: none; }
        }
    </style>
</head>
<body>

    <button class="btn-imprimir" onclick="window.print()" style="padding:10px 20px; margin-bottom:20px;">
        🖨️ Descargar / Imprimir PDF
    </button>

    <h1>Reporte de Stock — Mi Proyecto</h1>
    <p class="fecha">Generado el: <?= $fechaHoy ?></p>

    <table>
        <thead>
            <tr><th>#</th><th>Producto</th><th>Stock</th><th>Precio</th></tr>
        </thead>
        <tbody>
            <?php foreach ($stockActual as $p): ?>
                <tr>
                    <td><?= $p["id"] ?></td>
                    <td><?= htmlspecialchars($p["nombre"]) ?></td>
                    <td><?= $p["stock"] ?></td>
                    <td>$<?= number_format($p["precio"], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
