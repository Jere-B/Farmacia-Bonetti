<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = trim($_POST['correo']);
    $pass = trim($_POST['contrasena']);

    $stmt = $conn->prepare("
        SELECT id_usuario, nombre, contrasena, rol
        FROM usuarios
        WHERE correo = ?
    ");

    $stmt->bind_param("s", $correo);
    $stmt->execute();

    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    if ($user && $pass === $user['contrasena']) {

        $_SESSION['id'] = $user['id_usuario'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];

        header("Location: index.php");
        exit;

    } else {
        $error = "Correo o contraseña incorrectos.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang='es'><head>
<meta charset='UTF-8'>
<title>Login — Farmacia Bonetti</title>
<link rel='stylesheet'
href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
</head><body class='bg-light'>
<div class='container mt-5' style='max-width:420px'>
<div class='card shadow'>
<div class='card-header bg-success text-white text-center'>
<h4>&#x1F48A; Farmacia Bonetti</h4>
</div>
<div class='card-body p-4'>
<?php if (!empty($error)): ?>
<div class='alert alert-danger'><?= $error ?></div>
<?php endif; ?>
<form method='post'>
<div class='mb-3'>
<label class='form-label'>Correo</label>
<input type='email' name='correo' class='form-control' required>
</div>
<div class='mb-3'>
<label class='form-label'>Contraseña</label>
<input type='password' name='contrasena' class='form-control' required>
</div>
<button type='submit' class='btn btn-success w-100'>Ingresar</button>
</form>
</div>
</div>
</div></body></html>