<?php 
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['passenger_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/functions.php'; // Necesario para getRoutes()
require_once 'includes/header.php';    // Opcionalmente puedes moverlo arriba si quieres el layout completo

$rutas = getRoutes();
?>

<section aria-labelledby="routes-title">
    <h1 id="routes-title">Seleccione su Ruta</h1>
    
    <div class="routes-grid">
        <?php foreach ($rutas as $ruta): ?>
            <div class="route-card" tabindex="0" role="button" aria-label="Seleccionar ruta <?= $ruta['nombre'] ?>">
                <h2><?= $ruta['nombre'] ?></h2>
                <p>Precio: $<?= $ruta['precio'] ?></p>
                <a href="buses.php?ruta_id=<?= $ruta['id'] ?>" class="route-link">Seleccionar</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
