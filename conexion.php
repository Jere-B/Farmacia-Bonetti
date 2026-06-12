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
// Datos del servidor MySQL de InfinityFree
define('DB_HOST', 'sql.infinityfree.com'); // servidor
define('DB_USER', 'epiz_XXXXXXXX'); // usuario MySQL
define('DB_PASS', 'tu_contrasena_mysql'); // contraseña
define('DB_NAME', 'epiz_XXXXXXXX_farmacia_db'); // nombre BD
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
die('Error de conexion: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>