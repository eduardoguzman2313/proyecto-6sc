<?php
require_once "../model/verificar_sesion.php";
require_once "../model/ProveedorModel.php";

$modelo      = new ProveedorModel();
$proveedores = $modelo->obtenerTodos();
$fechaHoy    = date("d/m/Y H:i");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Reporte de Proveedores</title>
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
    <h1>Reporte de Proveedores — Mi Proyecto</h1>
    <p class="fecha">Generado el: <?= $fechaHoy ?> — Total: <?= count($proveedores) ?> proveedores</p>
    <table>
        <thead><tr><th>#</th><th>Nombre Comercial</th><th>Contacto</th><th>Teléfono</th><th>Email</th><th>Dirección</th><th>Fecha Registro</th></tr></thead>
        <tbody>
            <?php foreach ($proveedores as $p): ?>
                <tr>
                    <td><?= $p["id"] ?></td>
                    <td><?= htmlspecialchars($p["nombre_comercial"]) ?></td>
                    <td><?= htmlspecialchars($p["nombre"]) ?></td>
                    <td><?= htmlspecialchars($p["telefono"]) ?></td>
                    <td><?= htmlspecialchars($p["email"]) ?></td>
                    <td><?= htmlspecialchars($p["direccion"]) ?></td>
                    <td><?= $p["fecha_registro"] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
