<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';

// ELIMINAR
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM usuarios WHERE id_usuario = $id");
    header("Location: usuarios.php?msg=eliminado");
    exit();
}

// AGREGAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $nombre     = $conn->real_escape_string($_POST['nombre']);
    $correo     = $conn->real_escape_string($_POST['correo']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $rol        = $conn->real_escape_string($_POST['rol']);
    $fecha_reg  = date('Y-m-d');

    $conn->query("INSERT INTO usuarios (nombre, correo, contrasena, rol, fecha_reg)
                  VALUES ('$nombre', '$correo', '$contrasena', '$rol', '$fecha_reg')");
    header("Location: usuarios.php?msg=agregado");
    exit();
}

// EDITAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id     = intval($_POST['id_usuario']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $rol    = $conn->real_escape_string($_POST['rol']);

    $sql = "UPDATE usuarios SET nombre='$nombre', correo='$correo', rol='$rol' WHERE id_usuario=$id";

    // Solo actualizar contraseña si se ingresó una nueva
    if (!empty($_POST['contrasena'])) {
        $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nombre='$nombre', correo='$correo', contrasena='$contrasena', rol='$rol' WHERE id_usuario=$id";
    }

    $conn->query($sql);
    header("Location: usuarios.php?msg=editado");
    exit();
}

// Obtener usuario a editar
$usuario_editar = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM usuarios WHERE id_usuario = $id");
    $usuario_editar = $res->fetch_assoc();
}

// Listar todos los usuarios
$usuarios = $conn->query("SELECT * FROM usuarios ORDER BY fecha_reg DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background: linear-gradient(135deg, #e8f5e9, #ffffff);">

<?php include 'navbar.php'; ?>

<div class="container mt-5">

    <?php if (isset($_GET['msg'])): ?>
        <?php $mensajes = ['agregado' => ['success', 'Usuario agregado correctamente.'], 'editado' => ['success', 'Usuario actualizado correctamente.'], 'eliminado' => ['danger', 'Usuario eliminado.']]; ?>
        <?php if (isset($mensajes[$_GET['msg']])): [$tipo, $texto] = $mensajes[$_GET['msg']]; ?>
            <div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert">
                <?= $texto ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card border-0 shadow-lg p-4 mb-4" style="border-radius:25px;">
        <h4 class="fw-bold text-success mb-3">
            <?= $usuario_editar ? '✏️ Editar Usuario' : '➕ Agregar Usuario' ?>
        </h4>

        <form method="POST">
            <input type="hidden" name="accion" value="<?= $usuario_editar ? 'editar' : 'agregar' ?>">
            <?php if ($usuario_editar): ?>
                <input type="hidden" name="id_usuario" value="<?= $usuario_editar['id_usuario'] ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required
                           value="<?= $usuario_editar ? htmlspecialchars($usuario_editar['nombre']) : '' ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" required
                           value="<?= $usuario_editar ? htmlspecialchars($usuario_editar['correo']) : '' ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        Contraseña <?= $usuario_editar ? '<span class="text-muted small">(dejar vacío para no cambiar)</span>' : '' ?>
                    </label>
                    <input type="password" name="contrasena" class="form-control"
                           <?= $usuario_editar ? '' : 'required' ?>>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Rol</label>
                    <select name="rol" class="form-select" required>
                        <option value="admin"   <?= ($usuario_editar && $usuario_editar['rol'] === 'admin')   ? 'selected' : '' ?>>Admin</option>
                        <option value="vendedor" <?= ($usuario_editar && $usuario_editar['rol'] === 'vendedor') ? 'selected' : '' ?>>Vendedor</option>
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success rounded-pill px-4">
                    <?= $usuario_editar ? '💾 Guardar cambios' : '➕ Agregar' ?>
                </button>
                <?php if ($usuario_editar): ?>
                    <a href="usuarios.php" class="btn btn-secondary rounded-pill px-4 ms-2">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabla de usuarios -->
    <div class="card border-0 shadow-lg p-4" style="border-radius:25px;">
        <h4 class="fw-bold text-success mb-3">👥 Lista de Usuarios</h4>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = $usuarios->fetch_assoc()): ?>
                    <tr>
                        <td><?= $u['id_usuario'] ?></td>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['correo']) ?></td>
                        <td>
                            <span class="badge <?= $u['rol'] === 'admin' ? 'bg-danger' : 'bg-primary' ?> rounded-pill">
                                <?= ucfirst($u['rol']) ?>
                            </span>
                        </td>
                        <td><?= $u['fecha_reg'] ?></td>
                        <td>
                            <a href="usuarios.php?editar=<?= $u['id_usuario'] ?>"
                               class="btn btn-warning btn-sm rounded-pill px-3">✏️ Editar</a>
                            <a href="usuarios.php?eliminar=<?= $u['id_usuario'] ?>"
                               class="btn btn-danger btn-sm rounded-pill px-3 ms-1"
                               onclick="return confirm('¿Seguro que querés eliminar este usuario?')">🗑️ Eliminar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>