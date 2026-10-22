<?php
require_once "../model/verificar_sesion.php";
require_once "../model/ProductoModel.php";

$modelo    = new ProductoModel();
$productos = $modelo->obtenerTodos();
$fechaHoy  = date("d/m/Y H:i");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Reporte de Productos</title>
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
    <h1>Reporte de Productos — Mi Proyecto</h1>
    <p class="fecha">Generado el: <?= $fechaHoy ?> — Total: <?= count($productos) ?> productos</p>
    <table>
        <thead><tr><th>#</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Unidad</th><th>Stock</th><th>Fecha Registro</th></tr></thead>
        <tbody>
            <?php foreach ($productos as $p): ?>
                <tr>
                    <td><?= $p["id"] ?></td>
                    <td><?= htmlspecialchars($p["nombre"]) ?></td>
                    <td><?= htmlspecialchars($p["categoria"]) ?></td>
                    <td>$<?= number_format($p["precio"], 2) ?></td>
                    <td><?= htmlspecialchars($p["unidad"]) ?></td>
                    <td><?= $p["stock"] ?? 0 ?></td>
                    <td><?= $p["fecha_registro"] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
