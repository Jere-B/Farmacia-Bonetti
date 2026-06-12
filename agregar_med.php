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
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$n = $_POST['nombre']; $lab = $_POST['laboratorio'];
$cat= $_POST['categoria']; $pre = (float)$_POST['precio'];
$st = (int)$_POST['stock']; $fv = $_POST['fecha_venc'];
$desc = $_POST['descripcion'];
$stmt = $conn->prepare(
'INSERT INTO medicamentos
(nombre,laboratorio,categoria,precio,stock,fecha_venc,descripcion)
VALUES (?,?,?,?,?,?,?)');
$stmt->bind_param('ssssdss',$n,$lab,$cat,$pre,$st,$fv,$desc);
if ($stmt->execute()) {
header('Location: medicamentos.php'); exit;
} else { $msg = 'Error al guardar.'; }
}
?>
<!DOCTYPE html><html lang='es'><head>
<meta charset='UTF-8'><title>Agregar Medicamento</title>
<link rel='stylesheet'
href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
</head><body class='bg-light'>
<?php include 'navbar.php'; ?>
<div class='container mt-4' style='max-width:600px'>
<h3 class='mb-3'>Agregar Medicamento</h3>
<?php if($msg): ?><div class='alert alert-danger'><?=$msg?></div><?php endif;?>
<div class='card shadow-sm p-4'>
<form method='post'>
<div class='mb-3'>
<label>Nombre</label>
<input type='text' name='nombre' class='form-control' required>
</div>
<div class='row'>
<div class='col mb-3'>
<label>Laboratorio</label>
<input type='text' name='laboratorio' class='form-control' required>
</div>
<div class='col mb-3'>
<label>Categoría</label>
<select name='categoria' class='form-select'>
<option>Analgésico</option><option>Antibiótico</option>
<option>Antinflamatorio</option><option>Vitamina</option>
<option>Otro</option>
</select>
</div>
</div>
<div class='row'>
<div class='col mb-3'>
<label>Precio ($)</label>
<input type='number' step='0.01' name='precio' class='form-control' required>
</div>
<div class='col mb-3'>
<label>Stock</label>
<input type='number' name='stock' class='form-control' value='0'>
</div>
</div>
<div class='mb-3'>
<label>Fecha de vencimiento</label>
<input type='date' name='fecha_venc' class='form-control' required>
</div>
<div class='mb-3'>
<label>Descripción (opcional)</label>
<textarea name='descripcion' class='form-control' rows='2'></textarea>
</div>
<button type='submit' class='btn btn-primary w-100'>Guardar</button>
</form>
</div>
</div></body></html>