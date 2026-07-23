<?php
session_start();
if (!isset($_SESSION['id'])) { header('Location: login.php'); exit; }
require 'conexion.php';

$msg = '';
$tipo_msg = 'info';
$hoy = date('Y-m-d');
$id_usuario = $_SESSION['id'];
$denominaciones = [100, 200, 500, 1000, 2000, 10000, 20000];

// APERTURA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'apertura') {
    $turno = $conn->real_escape_string($_POST['turno']);
    $hora  = date('H:i:s');

    // Verificar si ya hay una caja abierta para ese turno hoy
    $check = $conn->query("SELECT id_caja FROM caja WHERE fecha='$hoy' AND turno='$turno' LIMIT 1");
    if ($check->num_rows > 0) {
        $msg = "Ya existe una caja para el turno $turno de hoy.";
        $tipo_msg = 'warning';
    } else {
        $b = [];
        $total = 0;
        foreach ($denominaciones as $d) {
            $cant = intval($_POST["b$d"]);
            $b[$d] = $cant;
            $total += $d * $cant;
        }

        $sql = "INSERT INTO caja (id_usuario, turno, fecha, hora_apertura,
                billetes_100, billetes_200, billetes_500, billetes_1000,
                billetes_2000, billetes_10000, billetes_20000,
                total_apertura, estado)
                VALUES ($id_usuario, '$turno', '$hoy', '$hora',
                {$b[100]}, {$b[200]}, {$b[500]}, {$b[1000]},
                {$b[2000]}, {$b[10000]}, {$b[20000]},
                $total, 'abierta')";
        $conn->query($sql);
        $msg = "Caja aperturada correctamente. Total inicial: $" . number_format($total, 2);
        $tipo_msg = 'success';
    }
}

// CIERRE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'cierre') {
    $id_caja = intval($_POST['id_caja']);
    $hora_cierre = date('H:i:s');

    $b = [];
    $total = 0;
    foreach ($denominaciones as $d) {
        $cant = intval($_POST["b$d"]);
        $b[$d] = $cant;
        $total += $d * $cant;
    }

    $sql = "UPDATE caja SET hora_cierre='$hora_cierre',
            billetes_100={$b[100]}, billetes_200={$b[200]}, billetes_500={$b[500]},
            billetes_1000={$b[1000]}, billetes_2000={$b[2000]},
            billetes_10000={$b[10000]}, billetes_20000={$b[20000]},
            total_cierre=$total, estado='cerrada'
            WHERE id_caja=$id_caja";
    $conn->query($sql);
    $msg = "Caja cerrada correctamente. Total final: $" . number_format($total, 2);
    $tipo_msg = 'success';
}

// Cajas de hoy
$cajas_hoy = $conn->query(
    "SELECT c.*, u.nombre AS cajero FROM caja c
     JOIN usuarios u ON c.id_usuario = u.id_usuario
     WHERE c.fecha = '$hoy' ORDER BY c.turno ASC"
)->fetch_all(MYSQLI_ASSOC);

// Caja abierta actual (para mostrar formulario de cierre)
$caja_abierta = null;
foreach ($cajas_hoy as $c) {
    if ($c['estado'] === 'abierta') {
        $caja_abierta = $c;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Caja — Farmacia Brunas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #e8f5e9, #ffffff);">
<?php include 'navbar.php'; ?>

<div class="container mt-4">

    <?php if ($msg): ?>
        <div class="alert alert-<?= $tipo_msg ?> alert-dismissible fade show">
            <?= $msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- APERTURA -->
        <?php if (!$caja_abierta): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-lg p-4" style="border-radius:20px;">
                <h5 class="fw-bold text-success mb-3">🔓 Apertura de Caja</h5>
                <form method="POST">
                    <input type="hidden" name="accion" value="apertura">
                    <div class="mb-3">
                        <label class="form-label">Turno</label>
                        <select name="turno" class="form-select" required>
                            <option value="mañana">🌅 Mañana</option>
                            <option value="tarde">🌆 Tarde</option>
                        </select>
                    </div>
                    <p class="fw-semibold text-secondary">Conteo de billetes:</p>
                    <?php foreach ($denominaciones as $d): ?>
                    <div class="row mb-2 align-items-center">
                        <div class="col-5">
                            <label class="form-label mb-0">💵 $<?= number_format($d) ?></label>
                        </div>
                        <div class="col-4">
                            <input type="number" name="b<?= $d ?>" class="form-control form-control-sm billete"
                                   data-valor="<?= $d ?>" min="0" value="0">
                        </div>
                        <div class="col-3 text-end subtotal-<?= $d ?> text-muted small">$0</div>
                    </div>
                    <?php endforeach; ?>
                    <div class="mt-3 p-2 bg-success bg-opacity-10 rounded d-flex justify-content-between fw-bold">
                        <span>Total apertura:</span>
                        <span id="total-apertura">$0.00</span>
                    </div>
                    <button type="submit" class="btn btn-success rounded-pill px-4 mt-3 w-100">🔓 Abrir Caja</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- CIERRE -->
        <?php if ($caja_abierta): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-lg p-4" style="border-radius:20px;">
                <h5 class="fw-bold text-danger mb-1">🔒 Cierre de Caja</h5>
                <p class="text-muted small mb-3">
                    Turno: <strong><?= ucfirst($caja_abierta['turno']) ?></strong> —
                    Abierta a las <strong><?= substr($caja_abierta['hora_apertura'], 0, 5) ?></strong> hs
                    por <strong><?= htmlspecialchars($caja_abierta['cajero']) ?></strong>
                </p>
                <form method="POST">
                    <input type="hidden" name="accion" value="cierre">
                    <input type="hidden" name="id_caja" value="<?= $caja_abierta['id_caja'] ?>">
                    <p class="fw-semibold text-secondary">Conteo de billetes al cierre:</p>
                    <?php foreach ($denominaciones as $d): ?>
                    <div class="row mb-2 align-items-center">
                        <div class="col-5">
                            <label class="form-label mb-0">💵 $<?= number_format($d) ?></label>
                        </div>
                        <div class="col-4">
                            <input type="number" name="b<?= $d ?>" class="form-control form-control-sm billete-cierre"
                                   data-valor="<?= $d ?>" min="0" value="0">
                        </div>
                        <div class="col-3 text-end text-muted small">$0</div>
                    </div>
                    <?php endforeach; ?>
                    <div class="mt-3 p-2 bg-danger bg-opacity-10 rounded d-flex justify-content-between fw-bold">
                        <span>Total cierre:</span>
                        <span id="total-cierre">$0.00</span>
                    </div>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 mt-3 w-100">🔒 Cerrar Caja</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- RESUMEN DEL DÍA -->
        <div class="col-12">
            <div class="card border-0 shadow-lg p-4" style="border-radius:20px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-success mb-0">📋 Resumen del Día — <?= date('d/m/Y') ?></h5>
                    <a href="ventas_pdf.php?fecha=<?= $hoy ?>" target="_blank"
                       class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        📄 PDF Ventas del Día
                    </a>
                </div>

                <?php if (empty($cajas_hoy)): ?>
                    <p class="text-muted">No hay cajas registradas hoy.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-success">
                            <tr>
                                <th>Turno</th>
                                <th>Cajero</th>
                                <th>Apertura</th>
                                <th>Total Apertura</th>
                                <th>Cierre</th>
                                <th>Total Cierre</th>
                                <th>Estado</th>
                                <th>PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cajas_hoy as $c): ?>
                            <tr>
                                <td><?= ucfirst($c['turno']) ?></td>
                                <td><?= htmlspecialchars($c['cajero']) ?></td>
                                <td><?= $c['hora_apertura'] ? substr($c['hora_apertura'], 0, 5).' hs' : '-' ?></td>
                                <td>$<?= number_format($c['total_apertura'], 2) ?></td>
                                <td><?= $c['hora_cierre'] ? substr($c['hora_cierre'], 0, 5).' hs' : '-' ?></td>
                                <td><?= $c['total_cierre'] ? '$'.number_format($c['total_cierre'], 2) : '-' ?></td>
                                <td>
                                    <span class="badge rounded-pill <?= $c['estado'] === 'abierta' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $c['estado'] === 'abierta' ? '🔓 Abierta' : '🔒 Cerrada' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="ventas_pdf.php?fecha=<?= $hoy ?>&turno=<?= $c['turno'] ?>"
                                       target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-2">
                                        📄 PDF
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function calcularTotal(clase, totalId) {
    document.querySelectorAll('.' + clase).forEach(input => {
        input.addEventListener('input', () => {
            let total = 0;
            document.querySelectorAll('.' + clase).forEach(i => {
                total += parseInt(i.value || 0) * parseInt(i.dataset.valor);
            });
            document.getElementById(totalId).textContent =
                '$' + total.toLocaleString('es-AR', {minimumFractionDigits: 2});
        });
    });
}
calcularTotal('billete', 'total-apertura');
calcularTotal('billete-cierre', 'total-cierre');
</script>
</body>
</html>