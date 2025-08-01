document.addEventListener('DOMContentLoaded', function() {
    const seats = document.querySelectorAll('.seat');
    const selectedSeatsInput = document.getElementById('selected-seats');
    const selectedSeatsDisplay = document.getElementById('selected-seats-display');
    let selectedSeats = [];
    
    seats.forEach(seat => {
        seat.addEventListener('click', function() {
            const fila = this.dataset.fila;
            const posicion = this.dataset.posicion;
            const seatId = `${fila}_${posicion}`;
            
            // Verificar si el asiento ya está seleccionado
            const index = selectedSeats.indexOf(seatId);
            
            if (index === -1) {
                // Limitar a 40 asientos
                if (selectedSeats.length < 40) {
                    selectedSeats.push(seatId);
                    this.classList.add('selected');
                    this.setAttribute('aria-pressed', 'true');
                } else {
                    alert('Solo puede reservar hasta 40 asientos');
                }
            } else {
                selectedSeats.splice(index, 1);
                this.classList.remove('selected');
                this.setAttribute('aria-pressed', 'false');
            }
            
            // Actualizar campo oculto y display
            selectedSeatsInput.value = JSON.stringify(selectedSeats);
            updateSelectedSeatsDisplay();
        });
        
        // Permitir selección con teclado
        seat.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                this.click();
                e.preventDefault();
            }
        });
    });
    
    function updateSelectedSeatsDisplay() {
        if (selectedSeats.length > 0) {
            selectedSeatsDisplay.innerHTML = `
                <p><strong>Asientos seleccionados:</strong></p>
                <ul>
                    ${selectedSeats.map(seat => {
                        const [fila, posicion] = seat.split('_');
                        let posicionText = '';
                        switch(posicion) {
                            case 'ventana_izq': posicionText = 'Ventana Izquierda'; break;
                            case 'ventana_der': posicionText = 'Ventana Derecha'; break;
                            case 'pasillo_izq': posicionText = 'Pasillo Izquierdo'; break;
                            case 'pasillo_der': posicionText = 'Pasillo Derecho'; break;
                        }
                        return `<li>Fila ${fila} - ${posicionText}</li>`;
                    }).join('')}
                </ul>
                <p>Total: ${selectedSeats.length} asiento(s)</p>
            `;
        } else {
            selectedSeatsDisplay.innerHTML = '<p>No hay asientos seleccionados</p>';
        }
    }
});