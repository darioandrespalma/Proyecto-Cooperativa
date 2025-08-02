<?php require_once 'includes/header.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
?>

    <section aria-labelledby="register-title">
        <h1 id="register-title">Registro de Pasajero</h1>
        
        <?php if (isset($error)): ?>
            <div class="error" role="alert"><?= $error ?></div>
        <?php endif; ?>
        
        <form action="process/register.php" method="post" id="register-form">
            <div class="form-group">
                <label for="cedula">Cédula*</label>
                <input type="text" id="cedula" name="cedula" required aria-required="true">
            </div>
            
            <div class="form-group">
                <label for="nombre">Nombre*</label>
                <input type="text" id="nombre" name="nombre" required aria-required="true">
            </div>
            
            <div class="form-group">
                <label for="apellido">Apellido*</label>
                <input type="text" id="apellido" name="apellido" required aria-required="true">
            </div>
            
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <textarea id="direccion" name="direccion"></textarea>
            </div>
            
            <div class="form-group">
                <label for="celular">Celular</label>
                <input type="tel" id="celular" name="celular">
            </div>
            
            <div class="form-group">
                <label for="email">Email*</label>
                <input type="email" id="email" name="email" required aria-required="true">
            </div>
            
            <button type="submit">Registrarse y Continuar</button>
        </form>
    </section>
<?php require_once 'includes/footer.php'; ?>