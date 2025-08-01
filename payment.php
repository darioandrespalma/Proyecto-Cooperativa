<?php 
session_start();
require_once 'includes/header.php';

if (!isset($_SESSION['reservation_id'])) {
    header('Location: routes.php');
    exit;
}
?>
    <section aria-labelledby="payment-title">
        <h1 id="payment-title">Proceso de Pago</h1>
        
        <div class="payment-options">
            <button id="btn-transferencia" class="payment-method active" aria-pressed="true">Transferencia Bancaria</button>
            <button id="btn-tarjeta" class="payment-method" aria-pressed="false">Tarjeta de Crédito/Débito</button>
        </div>
        
        <form id="payment-form" action="process/payment.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="metodo" id="payment-method" value="transferencia">
            
            <div id="transferencia-form" class="payment-form">
                <div class="form-group">
                    <label for="comprobante">Comprobante de Transferencia*</label>
                    <input type="file" id="comprobante" name="comprobante" accept="image/*,.pdf" required aria-required="true">
                </div>
            </div>
            
            <div id="tarjeta-form" class="payment-form" style="display:none;">
                <div class="form-group">
                    <label for="numero_tarjeta">Número de Tarjeta*</label>
                    <input type="text" id="numero_tarjeta" name="numero_tarjeta" placeholder="1234 5678 9012 3456">
                </div>
                
                <div class="form-group">
                    <label for="fecha_vencimiento">Fecha de Vencimiento*</label>
                    <input type="text" id="fecha_vencimiento" name="fecha_vencimiento" placeholder="MM/AA">
                </div>
                
                <div class="form-group">
                    <label for="cvv">CVV*</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3">
                </div>
            </div>
            
            <button type="submit">Realizar Pago</button>
        </form>
    </section>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnTransferencia = document.getElementById('btn-transferencia');
            const btnTarjeta = document.getElementById('btn-tarjeta');
            const formTransferencia = document.getElementById('transferencia-form');
            const formTarjeta = document.getElementById('tarjeta-form');
            const paymentMethod = document.getElementById('payment-method');
            
            btnTransferencia.addEventListener('click', function() {
                this.classList.add('active');
                this.setAttribute('aria-pressed', 'true');
                btnTarjeta.classList.remove('active');
                btnTarjeta.setAttribute('aria-pressed', 'false');
                formTransferencia.style.display = 'block';
                formTarjeta.style.display = 'none';
                paymentMethod.value = 'transferencia';
            });
            
            btnTarjeta.addEventListener('click', function() {
                this.classList.add('active');
                this.setAttribute('aria-pressed', 'true');
                btnTransferencia.classList.remove('active');
                btnTransferencia.setAttribute('aria-pressed', 'false');
                formTransferencia.style.display = 'none';
                formTarjeta.style.display = 'block';
                paymentMethod.value = 'tarjeta';
            });
        });
    </script>
<?php require_once 'includes/footer.php'; ?>