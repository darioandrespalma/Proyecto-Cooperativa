<?php 
require_once 'includes/header.php';
require_once 'includes/functions.php';

$ruta_id = $_GET['ruta_id'] ?? null;
if (!$ruta_id) {
    header('Location: routes.php');
    exit;
}

$horarios = getSchedules($ruta_id);
$buses = getBuses($ruta_id);
?>
    <section aria-labelledby="buses-title">
        <h1 id="buses-title">Seleccione Horario y Bus</h1>
        
        <form action="seats.php" method="get">
            <div class="form-group">
                <label for="schedule">Horario de Salida*</label>
                <select id="schedule" name="schedule_id" required aria-required="true">
                    <?php foreach ($horarios as $horario): ?>
                        <option value="<?= $horario['id'] ?>"><?= date('H:i', strtotime($horario['hora'])) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="bus">Bus*</label>
                <select id="bus" name="bus_id" required aria-required="true">
                    <?php foreach ($buses as $bus): ?>
                        <option value="<?= $bus['id'] ?>"><?= $bus['placa'] ?> (Capacidad: <?= $bus['capacidad'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit">Seleccionar Asientos</button>
        </form>
    </section>
<?php require_once 'includes/footer.php'; ?>