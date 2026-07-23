<?php
session_start();
if (!isset($_SESSION['id'])) { header('Location: login.php'); exit; }
require 'conexion.php';

$fecha = isset($_GET['fecha']) ? $conn->real_escape_string($_GET['fecha']) : date('Y-m-d');
$turno = isset($_GET['turno']) ? $conn->real_escape_string($_GET['turno']) : null;

// Obtener ventas del día (y turno si se especifica)
// Las ventas del turno mañana son de 00:00 a 13:59, tarde de 14:00 a 23:59
$where = "DATE(v.fecha_venta) = '$fecha'";
if ($turno === 'mañana') {
    $where .= " AND TIME(v.fecha_venta) < '14:00:00'";
} elseif ($turno === 'tarde') {
    $where .= " AND TIME(v.fecha_venta) >= '14:00:00'";
}

$ventas = $conn->query(
    "SELECT v.*, m.nombre AS medicamento, u.nombre AS cajero
     FROM ventas v
     JOIN medicamentos m ON v.id_medicamento = m.id_medicamento
     JOIN usuarios u ON v.id_usuario = u.id_usuario
     WHERE $where
     ORDER BY v.fecha_venta ASC"
)->fetch_all(MYSQLI_ASSOC);

$total_general = array_sum(array_column($ventas, 'total'));
$titulo_turno = $turno ? ' — Turno ' . ucfirst($turno) : '';

// Generar HTML que el navegador puede imprimir como PDF
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas <?= date('d/m/Y', strtotime($fecha)) ?><?= $titulo_turno ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #222; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #198754; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #198754; }
        .header p { color: #555; margin-top: 4px; }
        .info { display: flex; justify-content: space-between; margin-bottom: 18px; font-size: 12px; color: #444; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead tr { background: #198754; color: white; }
        th, td { padding: 8px 10px; border: 1px solid #ddd; text-align: left; }
        tbody tr:nth-child(even) { background: #f4fdf7; }
        .total-row { font-weight: bold; background: #e8f5e9 !important; }
        .footer { text-align: center; font-size: 11px; color: #888; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; }
        .sin-ventas { text-align: center; padding: 40px; color: #888; font-size: 15px; }
        @media print {
            .no-print { display: none; }
            body { padding: 10px; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom:20px; text-align:right;">
    <button onclick="window.print()" style="background:#198754;color:white;border:none;padding:8px 20px;border-radius:20px;cursor:pointer;font-size:14px;">
        🖨️ Imprimir / Guardar PDF
    </button>
    <button onclick="window.close()" style="background:#6c757d;color:white;border:none;padding:8px 20px;border-radius:20px;cursor:pointer;font-size:14px;margin-left:8px;">
        ✖ Cerrar
    </button>
</div>

<div class="header">
    <h1>💊 Farmacia Bonetti</h1>
    <p>Listado de Ventas — <?= date('d/m/Y', strtotime($fecha)) ?><?= $titulo_turno ?></p>
</div>

<div class="info">
    <span>📅 Fecha: <strong><?= date('d/m/Y', strtotime($fecha)) ?></strong></span>
    <?php if ($turno): ?>
    <span>🕐 Turno: <strong><?= ucfirst($turno) ?></strong></span>
    <?php endif; ?>
    <span>📦 Total ventas: <strong><?= count($ventas) ?></strong></span>
    <span>Generado: <?= date('d/m/Y H:i') ?></span>
</div>

<?php if (empty($ventas)): ?>
    <div class="sin-ventas">No hay ventas registradas para este período.</div>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Hora</th>
            <th>Medicamento</th>
            <th>Cantidad</th>
            <th>Precio Unit.</th>
            <th>Total</th>
            <th>Cajero</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ventas as $i => $v): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= date('H:i', strtotime($v['fecha_venta'])) ?></td>
            <td><?= htmlspecialchars($v['medicamento']) ?></td>
            <td><?= $v['cantidad'] ?></td>
            <td>$<?= number_format($v['precio_unit'], 2) ?></td>
            <td>$<?= number_format($v['total'], 2) ?></td>
            <td><?= htmlspecialchars($v['cajero']) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="5" style="text-align:right;">TOTAL GENERAL:</td>
            <td>$<?= number_format($total_general, 2) ?></td>
            <td></td>
        </tr>
    </tbody>
</table>
<?php endif; ?>

<div class="footer">
    Farmacia Bonetti — Documento generado el <?= date('d/m/Y H:i') ?> hs
</div>

</body>
</html>