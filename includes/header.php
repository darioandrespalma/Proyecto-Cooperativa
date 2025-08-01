<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Reserva de Autobuses</title>
    <link rel="stylesheet" href="/proyecto_cooperativa/assets/css/styles.css">
    <script src="/proyecto_cooperativa/assets/js/main.js" defer></script>
    <script src="/proyecto_cooperativa/assets/js/a11y.js" defer></script>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Cooperativa de Transporte</h1>
            </div>
            <nav aria-label="Navegación principal">
                <button id="menu-toggle" aria-expanded="false" aria-controls="main-menu">
                    <span class="hamburger"></span>
                    <span class="sr-only">Menú</span>
                </button>
                <ul id="main-menu">
                    <li><a href="/proyecto_cooperativa/index.php">Inicio</a></li>
                    <li><a href="/proyecto_cooperativa/register.php">Reservar Asiento</a></li>
                    <li><a href="/proyecto_cooperativa/routes_info.php">Rutas</a></li>
                    <li><a href="/proyecto_cooperativa/schedules_info.php">Frecuencias</a></li>
                    <li><a href="/proyecto_cooperativa/contact.php">Contacto</a></li>
                    <li><a href="/proyecto_cooperativa/help.php">Ayuda</a></li>
                    <li><a href="/proyecto_cooperativa/my_reservations.php">Mis Reservas</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>