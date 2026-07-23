
<?php
// Datos del servidor MySQL de InfinityFree
define('DB_HOST', 'sql306.infinityfree.com'); // servidor
define('DB_USER', 'if0_42263011'); // usuario MySQL
define('DB_PASS', 'TGBmNBLcJ5u'); // contraseña
define('DB_NAME', 'if0_42263011_farmacia_bonetti'); // nombre BD
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
die('Error de conexion: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>

