<!-- navbar.php — incluir en todas las páginas -->
<nav class="navbar navbar-expand-lg" style="background:#198754">
<a class="navbar-brand text-white fw-bold" href="index.php">
&#x1F48A; FarmaSystem</a>
<div class="navbar-nav ms-auto">
<a class="nav-link text-white" href="medicamentos.php">Medicamentos</a>
<a class="nav-link text-white" href="ventas.php">Ventas</a>
<a class="nav-link text-white" href="logout.php">Cerrar sesión</a>
</div>
</nav>

<?php
session_start();
if (!isset($_SESSION['id'])) { header('Location: login.php'); exit; }
require 'conexion.php';
// Eliminar medicamento
if (isset($_GET['eliminar'])) {
$id = (int)$_GET['eliminar'];
$conn->query("DELETE FROM medicamentos WHERE id_medicamento=$id");
header('Location: medicamentos.php'); exit;
}
// Buscar medicamentos
$busqueda = isset($_GET['q']) ? '%'.$conn->real_escape_string($_GET['q']).'%' : '%';
$stmt = $conn->prepare(
'SELECT * FROM medicamentos WHERE nombre LIKE ? ORDER BY nombre');
$stmt->bind_param('s', $busqueda);
$stmt->execute();
$meds = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html><html lang='es'><head>
<meta charset='UTF-8'><title>Medicamentos</title>
<link rel='stylesheet'
href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
</head><body class='bg-light'>
<?php include 'navbar.php'; ?>
<div class='container mt-4'>
<div class='d-flex justify-content-between mb-3'>
<h3>Medicamentos</h3>
<a href='agregar_med.php' class='btn btn-primary'>+ Agregar</a>
</div>
<form class='mb-3'>
<input type='text' name='q' class='form-control'
placeholder='Buscar medicamento...'
value='<?= htmlspecialchars($_GET["q"] ?? "") ?>'>
</form>
<table class='table table-striped table-bordered'>
<thead class='table-success'>
<tr><th>Nombre</th><th>Laboratorio</th><th>Precio</th>
<th>Stock</th><th>Vencimiento</th><th>Acciones</th></tr>
</thead><tbody>
<?php foreach ($meds as $m): ?>
<tr class='<?= $m["stock"]<5 ? "table-danger" : "" ?>'>
<td><?= htmlspecialchars($m['nombre']) ?></td>
<td><?= htmlspecialchars($m['laboratorio']) ?></td>
<td>$<?= number_format($m['precio'],2) ?></td>
<td><?= $m['stock'] ?></td>
<td><?= $m['fecha_venc'] ?></td>
<td>
<a href='editar_med.php?id=<?= $m['id_medicamento'] ?>'
class='btn btn-sm btn-warning'>Editar</a>
<a href='?eliminar=<?= $m['id_medicamento'] ?>'
class='btn btn-sm btn-danger'
onclick='return confirm("¿Eliminar?")'>Eliminar</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div></body></html>