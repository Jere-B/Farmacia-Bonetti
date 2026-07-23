<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require 'conexion.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM medicamentos WHERE id_medicamento = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$med = $stmt->get_result()->fetch_assoc();

if (!$med) {
    die("Medicamento no encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'];
    $laboratorio = $_POST['laboratorio'];
    $categoria = $_POST['categoria'];
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    $fecha_venc = $_POST['fecha_venc'];
    $descripcion = $_POST['descripcion'];

    $stmt = $conn->prepare("
        UPDATE medicamentos
        SET nombre=?,
            laboratorio=?,
            categoria=?,
            precio=?,
            stock=?,
            fecha_venc=?,
            descripcion=?
        WHERE id_medicamento=?
    ");

    $stmt->bind_param(
        "sssdissi",
        $nombre,
        $laboratorio,
        $categoria,
        $precio,
        $stock,
        $fecha_venc,
        $descripcion,
        $id
    );

    if ($stmt->execute()) {
        header('Location: medicamentos.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Medicamento</title>

    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-4" style="max-width:700px;">

    <div class="card shadow-sm p-4">

        <h3 class="mb-3">Editar Medicamento</h3>

        <form method="post">

            <div class="mb-3">
                <label>Nombre</label>
                <input type="text"
                       name="nombre"
                       class="form-control"
                       value="<?= htmlspecialchars($med['nombre']) ?>"
                       required>
            </div>

            <div class="mb-3">
                <label>Laboratorio</label>
                <input type="text"
                       name="laboratorio"
                       class="form-control"
                       value="<?= htmlspecialchars($med['laboratorio']) ?>"
                       required>
            </div>

            <div class="mb-3">
                <label>Categoría</label>
                <input type="text"
                       name="categoria"
                       class="form-control"
                       value="<?= htmlspecialchars($med['categoria']) ?>"
                       required>
            </div>

            <div class="mb-3">
                <label>Precio</label>
                <input type="number"
                       step="0.01"
                       name="precio"
                       class="form-control"
                       value="<?= $med['precio'] ?>"
                       required>
            </div>

            <div class="mb-3">
                <label>Stock</label>
                <input type="number"
                       name="stock"
                       class="form-control"
                       value="<?= $med['stock'] ?>"
                       required>
            </div>

            <div class="mb-3">
                <label>Fecha de vencimiento</label>
                <input type="date"
                       name="fecha_venc"
                       class="form-control"
                       value="<?= $med['fecha_venc'] ?>"
                       required>
            </div>

            <div class="mb-3">
                <label>Descripción</label>
                <textarea name="descripcion"
                          class="form-control"
                          rows="3"><?= htmlspecialchars($med['descripcion']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-warning">
                Guardar cambios
            </button>

            <a href="medicamentos.php" class="btn btn-secondary">
                Cancelar
            </a>

        </form>

    </div>

</div>

</body>
</html>