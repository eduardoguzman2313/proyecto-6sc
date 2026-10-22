<?php
require_once "../model/verificar_sesion.php";
require_once "../model/ClienteModel.php";

$modelo   = new ClienteModel();
$clientes = $modelo->obtenerTodos();
$fechaHoy = date("d/m/Y H:i");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Reporte de Clientes</title>
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
    <h1>Reporte de Clientes — Mi Proyecto</h1>
    <p class="fecha">Generado el: <?= $fechaHoy ?> — Total: <?= count($clientes) ?> clientes</p>
    <table>
        <thead><tr><th>#</th><th>Nombre</th><th>Apellido</th><th>Cédula</th><th>Teléfono</th><th>Email</th><th>Fecha Registro</th></tr></thead>
        <tbody>
            <?php foreach ($clientes as $c): ?>
                <tr>
                    <td><?= $c["id"] ?></td>
                    <td><?= htmlspecialchars($c["nombre"]) ?></td>
                    <td><?= htmlspecialchars($c["apellido"]) ?></td>
                    <td><?= htmlspecialchars($c["cedula"]) ?></td>
                    <td><?= htmlspecialchars($c["telefono"]) ?></td>
                    <td><?= htmlspecialchars($c["email"]) ?></td>
                    <td><?= $c["fecha_registro"] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
