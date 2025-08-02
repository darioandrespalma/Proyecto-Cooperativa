<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Sistema de reservas de la Cooperativa de Transporte Espejo - Viajes seguros y cómodos por todo Ecuador">
    <title>Cooperativa de Transporte Espejo</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/help.css">
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

                <!-- Sección de usuario -->
                <div class="user-section">
                    <?php if (isset($_SESSION['passenger_id'])): ?>
                        <div class="user-info" style="display: flex; align-items: center; gap: 10px; margin-right: 20px;">
                            <div class="user-avatar"
                                style="width: 40px; height: 40px; background-color: #007bff; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                <?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?>
                            </div>
                            <div class="user-name" style="color: white;">
                                <?= htmlspecialchars($_SESSION['nombre']) ?>
                                <br>
                                <a href="process/logout.php" style="color: #ffdd57; font-size: 0.9em;">Cerrar sesión</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="login-button" style="margin-right: 20px;">
                            <a href="Location: ../login.php" class="primary-action-btn">Iniciar Sesión</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Segunda fila: Menú de navegación -->
        <div class="header-bottom">
            <nav class="main-nav" aria-label="Navegación principal">
                <button id="menu-toggle" aria-expanded="false" aria-controls="main-menu"
                    aria-label="Mostrar/ocultar menú de navegación">
                    <span class="hamburger"></span>
                </button>
                <ul id="main-menu">
                    <?php if (!isset($_SESSION['passenger_id'])): ?>
                        <li><a href="index.php">Inicio</a></li>
                    <?php endif; ?>
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