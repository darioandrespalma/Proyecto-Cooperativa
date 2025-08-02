document.addEventListener('DOMContentLoaded', function () {
    const seats = document.querySelectorAll('.seat');
    const container = document.getElementById('asientos-container'); // contenedor donde se insertarán los inputs
    const selectedSeatsDisplay = document.getElementById('selected-seats-display');
    let selectedSeats = [];
    
    // Inicializar los asientos ocupados
    if (seat.classList.contains('occupied')) return;

    seats.forEach(seat => {
        seat.addEventListener('click', function () {
            const fila = this.dataset.fila;
            const posicion = this.dataset.posicion;
            const seatId = `${fila}_${posicion}`;

            const index = selectedSeats.indexOf(seatId);

            if (index === -1) {
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

            updateSeatInputs();
            updateSelectedSeatsDisplay();
        });

        seat.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                this.click();
                e.preventDefault();
            }
        });
    });

    function updateSeatInputs() {
        // Limpiar inputs anteriores
        container.innerHTML = '';

        selectedSeats.forEach(seat => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'asientos[]';
            input.value = seat;
            container.appendChild(input);
        });
    }

    function updateSelectedSeatsDisplay() {
        if (selectedSeats.length > 0) {
            selectedSeatsDisplay.innerHTML = `
            <p><strong>Asientos seleccionados:</strong></p>
            <ul>
                ${selectedSeats.map(seat => {
                const underscoreIndex = seat.indexOf('_');
                const fila = seat.substring(0, underscoreIndex);
                const posicion = seat.substring(underscoreIndex + 1);
                let posicionText = '';
                switch (posicion) {
                    case 'ventana_izq': posicionText = 'Ventana Izquierda'; break;
                    case 'ventana_der': posicionText = 'Ventana Derecha'; break;
                    case 'pasillo_izq': posicionText = 'Pasillo Izquierdo'; break;
                    case 'pasillo_der': posicionText = 'Pasillo Derecho'; break;
                    default: posicionText = posicion; break;
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
