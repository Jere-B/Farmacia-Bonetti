<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require 'conexion.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_med = (int)$_POST['id_medicamento'];
    $cant = (int)$_POST['cantidad'];
    $id_user = $_SESSION['id'];

    // Obtener precio actual
    $r = $conn->query("
        SELECT precio, stock
        FROM medicamentos
        WHERE id_medicamento = $id_med
    ");

    $med = $r->fetch_assoc();

    if ($med && $med['stock'] >= $cant) {

        $precio = $med['precio'];
        $total = $precio * $cant;

        $stmt = $conn->prepare("
            INSERT INTO ventas
            (id_usuario, id_medicamento, cantidad, precio_unit, total)
            VALUES (?, ?, ?, ?, ?)
        ");

        // ESTA ES LA ÚNICA LÍNEA CORREGIDA
        $stmt->bind_param("iiidd", $id_user, $id_med, $cant, $precio, $total);

        $stmt->execute();

        // Descontar stock
        $conn->query("
            UPDATE medicamentos
            SET stock = stock - $cant
            WHERE id_medicamento = $id_med
        ");

        $msg = 'Venta registrada. Total: $' . number_format($total, 2);

    } else {

        $msg = 'Stock insuficiente.';

    }
}

// Medicamentos para el select
$meds = $conn->query("
    SELECT id_medicamento, nombre, precio, stock
    FROM medicamentos
    WHERE stock > 0
    ORDER BY nombre
")->fetch_all(MYSQLI_ASSOC);

// Últimas ventas
$ventas = $conn->query("
    SELECT v.*, m.nombre AS med, u.nombre AS cajero
    FROM ventas v
    JOIN medicamentos m ON v.id_medicamento = m.id_medicamento
    JOIN usuarios u ON v.id_usuario = u.id_usuario
    ORDER BY v.fecha_venta DESC
    LIMIT 20
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<title>Ventas</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

</head>

<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-4">

<div class="row">

<div class="col-md-4">

<div class="card shadow-sm p-3 mb-4">

<h5>Nueva Venta</h5>

<?php if($msg): ?>
<div class="alert alert-info">
    <?= $msg ?>
</div>
<?php endif; ?>

<form method="post">

<div class="mb-2">

<label>Medicamento</label>

<select
name="id_medicamento"
class="form-select"
required
onchange="this.form.precio.value=this.options[this.selectedIndex].dataset.precio">

<option value="">-- Seleccionar --</option>

<?php foreach($meds as $m): ?>

<option
value="<?= $m['id_medicamento'] ?>"
data-precio="<?= $m['precio'] ?>">

<?= htmlspecialchars($m['nombre']) ?>
(stock: <?= $m['stock'] ?>)

</option>

<?php endforeach; ?>

</select>

</div>

<div class="mb-2">

<label>Precio unitario</label>

<input
type="text"
name="precio"
class="form-control"
readonly>

</div>

<div class="mb-3">

<label>Cantidad</label>

<input
type="number"
name="cantidad"
class="form-control"
min="1"
value="1"
required>

</div>

<button
type="submit"
class="btn btn-success w-100">
Registrar
</button>

</form>

</div>

</div>

<div class="col-md-8">

<h5>Últimas ventas</h5>

<table class="table table-sm table-bordered">

<thead class="table-success">

<tr>

<th>Medicamento</th>
<th>Cantidad</th>
<th>Total</th>
<th>Cajero</th>
<th>Fecha</th>

</tr>

</thead>

<tbody>

<?php foreach($ventas as $v): ?>

<tr>

<td><?= htmlspecialchars($v['med']) ?></td>

<td><?= $v['cantidad'] ?></td>

<td>$<?= number_format($v['total'],2) ?></td>

<td><?= htmlspecialchars($v['cajero']) ?></td>

<td><?= $v['fecha_venta'] ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>

</body>

</html>