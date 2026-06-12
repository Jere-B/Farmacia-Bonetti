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
require 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$correo = trim($_POST['correo']);
$pass = trim($_POST['contrasena']);
$stmt = $conn->prepare(
'SELECT id_usuario, nombre, contrasena, rol
FROM usuarios WHERE correo = ?');
$stmt->bind_param('s', $correo);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
if ($user && password_verify($pass, $user['contrasena'])) {
$_SESSION['id'] = $user['id_usuario'];
$_SESSION['nombre'] = $user['nombre'];
$_SESSION['rol'] = $user['rol'];
header('Location: index.php');
} else {
$error = 'Correo o contraseña incorrectos.';
}
}
?>
