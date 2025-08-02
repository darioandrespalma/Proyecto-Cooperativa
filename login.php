<?php
session_start();
require_once 'includes/header.php';
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<section aria-labelledby="login-title">
    <h1 id="login-title">Iniciar Sesión</h1>

    <?php if (isset($_SESSION['login_error'])): ?>
        <div class="error"><?= $_SESSION['login_error']; unset($_SESSION['login_error']); ?></div>
    <?php endif; ?>

    <form action="process/login.php" method="post">
        <div class="form-group">
            <label for="email">Correo electrónico*</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="cedula">Cédula*</label>
            <input type="text" id="cedula" name="cedula" required>
        </div>

        <button type="submit">Iniciar Sesión</button>
    </form>
</section>

<?php require_once 'includes/footer.php'; ?>
