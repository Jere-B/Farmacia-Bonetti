<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio | Farmacia Bonetti</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">

    <!-- Bienvenida -->
    <div class="hero">

        <div class="hero-content">

            <div class="logo-circle">
                💊
            </div>

            <h1>Farmacia Bonetti</h1>

            <p>
                Sistema de gestión y control de medicamentos, ventas y usuarios.
            </p>

        </div>

    </div>

    <div class="mt-5 mb-4">
        <h3 class="titulo-panel">
            Panel Principal
        </h3>
    </div>

    <div class="row g-4">

        <div class="col-lg-3 col-md-6">

            <a href="medicamentos.php" class="text-decoration-none">

                <div class="menu-card">

                    <div class="icono">
                        💊
                    </div>

                    <h4>
                        Medicamentos
                    </h4>

                    <p>
                        Gestión del inventario.
                    </p>

                </div>

            </a>

        </div>

        <div class="col-lg-3 col-md-6">

            <a href="ventas.php" class="text-decoration-none">

                <div class="menu-card">

                    <div class="icono">
                        🛒
                    </div>

                    <h4>
                        Ventas
                    </h4>

                    <p>
                        Registro de ventas realizadas.
                    </p>

                </div>

            </a>

        </div>

        <div class="col-lg-3 col-md-6">

            <a href="usuarios.php" class="text-decoration-none">

                <div class="menu-card">

                    <div class="icono">
                        👥
                    </div>

                    <h4>
                        Usuarios
                    </h4>

                    <p>
                        Control de acceso al sistema.
                    </p>

                </div>

            </a>

        </div>

        <div class="col-lg-3 col-md-6">

            <a href="caja.php" class="text-decoration-none">

                <div class="menu-card">

                    <div class="icono">
                        🏧
                    </div>

                    <h4>
                        Caja
                    </h4>

                    <p>
                        Apertura y cierre de caja.
                    </p>

                </div>

            </a>

        </div>

    </div>

</div>

<style>
    body{
    margin:0;
    padding:0;
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background:linear-gradient(135deg,#eef7f0,#ffffff);
    min-height:100vh;
}

/* HERO */

.hero{
    background:linear-gradient(135deg,#198754,#3bb273);
    border-radius:30px;
    padding:60px 40px;
    color:white;
    text-align:center;
    box-shadow:0 15px 35px rgba(0,0,0,.15);
}

.hero-content{
    max-width:700px;
    margin:auto;
}

.logo-circle{
    width:100px;
    height:100px;
    margin:auto;
    background:white;
    color:#198754;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:50px;
    margin-bottom:25px;
    box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.hero h1{
    font-size:3rem;
    font-weight:700;
    margin-bottom:15px;
}

.hero p{
    font-size:1.2rem;
    opacity:.95;
}

/* TITULO */

.titulo-panel{
    font-weight:700;
    color:#198754;
}

/* TARJETAS */

.menu-card{
    background:white;
    border-radius:25px;
    padding:35px 25px;
    text-align:center;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
    transition:.3s;
    height:100%;
}

.menu-card:hover{
    transform:translateY(-10px);
    box-shadow:0 20px 40px rgba(0,0,0,.15);
}

.icono{
    width:85px;
    height:85px;
    margin:auto;
    border-radius:50%;
    background:#e8f5e9;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:42px;
    margin-bottom:20px;
}

.menu-card h4{
    color:#198754;
    font-weight:700;
    margin-bottom:12px;
}

.menu-card p{
    color:#6c757d;
    margin:0;
}

a{
    color:inherit;
}

a:hover{
    color:inherit;
}

@media(max-width:768px){

.hero{
    padding:40px 20px;
}

.hero h1{
    font-size:2.2rem;
}

.logo-circle{
    width:80px;
    height:80px;
    font-size:40px;
}

.menu-card{
    margin-bottom:20px;
}

}
</style>

</body>
</html>