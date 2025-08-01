<?php require_once 'includes/header.php'; ?>
    <section aria-labelledby="schedules-title">
        <div class="breadcrumb">
            <a href="index.php">Inicio</a> > Frecuencias
        </div>
        
        <h1 id="schedules-title">Horarios y Frecuencias</h1>
        <p class="intro">Consulte los horarios de salida para cada una de nuestras rutas principales.</p>
        
        <div class="tabs">
            <div role="tablist" aria-label="Rutas disponibles">
                <button role="tab" aria-selected="true" aria-controls="quito-guayaquil-panel" id="quito-guayaquil-tab">Quito-Guayaquil</button>
                <button role="tab" aria-selected="false" aria-controls="quito-tulcan-panel" id="quito-tulcan-tab">Quito-Tulcán</button>
                <button role="tab" aria-selected="false" aria-controls="quito-loja-panel" id="quito-loja-tab">Quito-Loja</button>
                <button role="tab" aria-selected="false" aria-controls="quito-lagoagrio-panel" id="quito-lagoagrio-tab">Quito-Lago Agrío</button>
                <button role="tab" aria-selected="false" aria-controls="quito-esmeraldas-panel" id="quito-esmeraldas-tab">Quito-Esmeraldas</button>
            </div>
            
            <div tabindex="0" role="tabpanel" id="quito-guayaquil-panel" aria-labelledby="quito-guayaquil-tab">
                <h2>Horarios Quito - Guayaquil</h2>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Salida desde Quito</th>
                            <th>Llegada a Guayaquil</th>
                            <th>Frecuencia</th>
                            <th>Tipo de Servicio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>06:00</td>
                            <td>14:00</td>
                            <td>Diario</td>
                            <td>Económico</td>
                        </tr>
                        <tr>
                            <td>08:00</td>
                            <td>16:00</td>
                            <td>Diario</td>
                            <td>Ejecutivo</td>
                        </tr>
                        <tr>
                            <td>10:00</td>
                            <td>18:00</td>
                            <td>Diario</td>
                            <td>Económico</td>
                        </tr>
                        <tr>
                            <td>14:00</td>
                            <td>22:00</td>
                            <td>Diario</td>
                            <td>Ejecutivo</td>
                        </tr>
                        <tr>
                            <td>20:00</td>
                            <td>04:00</td>
                            <td>Diario</td>
                            <td>Cama</td>
                        </tr>
                        <tr>
                            <td>22:00</td>
                            <td>06:00</td>
                            <td>Diario</td>
                            <td>Cama</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div tabindex="0" role="tabpanel" id="quito-tulcan-panel" aria-labelledby="quito-tulcan-tab" hidden>
                <!-- Contenido similar para Quito-Tulcán -->
            </div>
            
            <!-- Paneles para las demás rutas -->
        </div>
        
        <div class="schedule-notes">
            <h3>Notas Importantes</h3>
            <ul>
                <li>Los horarios están sujetos a cambios sin previo aviso</li>
                <li>Se recomienda llegar al terminal 30 minutos antes de la salida</li>
                <li>Los buses de tipo "Cama" ofrecen asientos completamente reclinables</li>
                <li>Servicio Ejecutivo incluye WiFi y refrigerio</li>
            </ul>
        </div>
    </section>
    
    <script>
        // Script para manejar las pestañas
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('[role="tab"]');
            const panels = document.querySelectorAll('[role="tabpanel"]');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Desactivar todas las pestañas y paneles
                    tabs.forEach(t => {
                        t.setAttribute('aria-selected', 'false');
                        t.classList.remove('active');
                    });
                    panels.forEach(p => p.hidden = true);
                    
                    // Activar la pestaña seleccionada
                    this.setAttribute('aria-selected', 'true');
                    this.classList.add('active');
                    
                    // Mostrar el panel correspondiente
                    const panelId = this.getAttribute('aria-controls');
                    document.getElementById(panelId).hidden = false;
                });
            });
        });
    </script>
<?php require_once 'includes/footer.php'; ?>