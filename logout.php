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
session_destroy();
header('Location: login.php');
exit;
?>