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

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require 'conexion.php';

// Validar ID
if (!isset($_GET['id'])) {
    header("Location: medicamentos.php");
    exit;
}

$id = (int) $_GET['id'];

// Obtener medicamento con prepare
$stmt = $conn->prepare("SELECT * FROM medicamentos WHERE id_medicamento = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$med = $resultado->fetch_assoc();

// Si no existe
if (!$med) {
    header("Location: medicamentos.php");
    exit;
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nombre = $_POST['nombre'];
    $laboratorio = $_POST['laboratorio'];
    $categoria = $_POST['categoria'];
    $precio = (float) $_POST['precio'];
    $stock = (int) $_POST['stock'];
    $fecha = $_POST['fecha_venc'];
    $descripcion = $_POST['descripcion'];

    $stmt = $conn->prepare("
        UPDATE medicamentos
        SET nombre=?, laboratorio=?, categoria=?, precio=?, stock=?, fecha_venc=?, descripcion=?
        WHERE id_medicamento=?
    ");

    $stmt->bind_param(
        "sssdissi",
        $nombre,
        $laboratorio,
        $categoria,
        $precio,
        $stock,
        $fecha,
        $descripcion,
        $id
    );

    if ($stmt->execute()) {
        header("Location: medicamentos.php");
        exit;
    } else {
        $msg = "Error al actualizar.";
    }
}
?>