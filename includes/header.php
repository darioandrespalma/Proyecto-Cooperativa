<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de reservas de la Cooperativa de Transporte Espejo - Viajes seguros y cómodos por todo Ecuador">
    <title>Cooperativa de Transporte Espejo</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <script src="assets/js/main.js" defer></script>
    <script src="assets/js/a11y.js" defer></script>
</head>
<body>
    <!-- Header Principal -->
    <header class="main-header">
        <!-- Primera fila: Logo y Título -->
        <div class="header-top-container">
            <div class="header-top">
                <!-- Logo a la izquierda -->
                <div class="logo">
                    <img src="assets/images/logoEspejo.png" alt="Logo Cooperativa Espejo" width="120">
                </div>
                
                <!-- Título centrado -->
                <div class="header-title-container">
                    <h1 class="header-title">Cooperativa de Transporte Espejo</h1>
                </div>
                
                <!-- Espacio vacío para balancear el layout -->
                <div class="header-spacer"></div>
            </div>
        </div>
        
        <!-- Segunda fila: Menú de navegación -->
        <div class="header-bottom">
            <nav class="main-nav" aria-label="Navegación principal">
                <button id="menu-toggle" aria-expanded="false" aria-controls="main-menu" aria-label="Mostrar/ocultar menú de navegación">
                    <span class="hamburger"></span>
                </button>
                <ul id="main-menu">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="register.php">Reservar Asiento</a></li>
                    <li><a href="routes_info.php">Rutas</a></li>
                    <li><a href="schedules_info.php">Frecuencias</a></li>
                    <li><a href="contact.php">Contacto</a></li>
                    <li><a href="help.php">Ayuda</a></li>
                    <li><a href="my_reservations.php">Mis Reservas</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>