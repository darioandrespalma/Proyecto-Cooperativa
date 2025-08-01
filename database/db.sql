CREATE DATABASE IF NOT EXISTS cooperativa_transporte;
USE cooperativa_transporte;

-- Tabla de pasajeros
CREATE TABLE passengers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(10) UNIQUE NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    direccion TEXT,
    celular VARCHAR(10),
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de rutas
CREATE TABLE routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    duracion_estimada VARCHAR(20) NOT NULL,
    descripcion TEXT
);

-- Insertar más rutas con información detallada
INSERT INTO routes (nombre, precio, duracion_estimada, descripcion) VALUES
('Quito-Guayaquil', 25.00, '8 horas', 'Viaje directo desde la capital al principal puerto marítimo del país, atravesando hermosos paisajes andinos y costeros'),
('Quito-Tulcán', 15.00, '5 horas', 'Ruta hacia la frontera norte, famosa por su cementerio de topiarios y cercanía a Colombia'),
('Quito-Loja', 30.00, '12 horas', 'Conozca la capital musical del Ecuador con su rica herencia cultural y arquitectónica'),
('Quito-Lago Agrío', 20.00, '7 horas', 'Puerta de entrada a la Amazonía ecuatoriana y sus maravillas naturales'),
('Quito-Esmeraldas', 15.00, '6 horas', 'Disfrute de las hermosas playas y la cultura afroecuatoriana'),
('Quito-Cuenca', 28.00, '10 horas', 'Visite la hermosa ciudad declarada Patrimonio Cultural de la Humanidad'),
('Quito-Manta', 22.00, '7 horas', 'Conecta con el principal puerto pesquero y playas de la costa central'),
('Quito-Ambato', 8.00, '2 horas', 'Corta distancia a la ciudad de las flores y las frutas');

-- Tabla de buses
CREATE TABLE buses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ruta_id INT NOT NULL,
    placa VARCHAR(10) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    capacidad INT DEFAULT 40,
    tipo ENUM('Económico', 'Ejecutivo', 'Cama') DEFAULT 'Económico',
    FOREIGN KEY (ruta_id) REFERENCES routes(id)
);

-- Insertar más buses con información detallada
INSERT INTO buses (ruta_id, placa, modelo, capacidad, tipo) VALUES
-- Quito-Guayaquil (10 buses)
(1, 'ABC-123', 'Volvo B8R', 40, 'Ejecutivo'),
(1, 'ABC-124', 'Mercedes-Benz O500', 40, 'Ejecutivo'),
(1, 'ABC-125', 'Scania K320', 40, 'Cama'),
(1, 'ABC-126', 'Higer KLQ6125', 40, 'Económico'),
(1, 'ABC-127', 'Yutong ZK6122', 40, 'Ejecutivo'),
(1, 'ABC-128', 'Volvo B9S', 40, 'Cama'),
(1, 'ABC-129', 'Mercedes-Benz O400', 40, 'Económico'),
(1, 'ABC-130', 'Scania K420', 40, 'Cama'),
(1, 'ABC-131', 'Higer KLQ6125', 40, 'Ejecutivo'),
(1, 'ABC-132', 'Yutong ZK6122', 40, 'Económico'),
-- Quito-Tulcán (4 buses)
(2, 'DEF-133', 'Hino RN8J', 40, 'Económico'),
(2, 'DEF-134', 'Mercedes-Benz LO915', 40, 'Económico'),
(2, 'DEF-135', 'Volvo B7R', 40, 'Ejecutivo'),
(2, 'DEF-136', 'Scania K280', 40, 'Ejecutivo'),
-- Quito-Loja (4 buses)
(3, 'GHI-137', 'Yutong ZK6122', 40, 'Cama'),
(3, 'GHI-138', 'Volvo B9S', 40, 'Cama'),
(3, 'GHI-139', 'Scania K420', 40, 'Cama'),
(3, 'GHI-140', 'Mercedes-Benz O500', 40, 'Ejecutivo'),
-- Quito-Lago Agrío (4 buses)
(4, 'JKL-141', 'Higer KLQ6125', 40, 'Económico'),
(4, 'JKL-142', 'Mercedes-Benz LO915', 40, 'Económico'),
(4, 'JKL-143', 'Volvo B7R', 40, 'Ejecutivo'),
(4, 'JKL-144', 'Scania K280', 40, 'Ejecutivo'),
-- Quito-Esmeraldas (4 buses)
(5, 'MNO-145', 'Yutong ZK6122', 40, 'Económico'),
(5, 'MNO-146', 'Volvo B9S', 40, 'Ejecutivo'),
(5, 'MNO-147', 'Scania K420', 40, 'Cama'),
(5, 'MNO-148', 'Mercedes-Benz O500', 40, 'Ejecutivo'),
-- Quito-Cuenca (4 buses)
(6, 'PQR-149', 'Hino RN8J', 40, 'Económico'),
(6, 'PQR-150', 'Mercedes-Benz LO915', 40, 'Económico'),
(6, 'PQR-151', 'Volvo B7R', 40, 'Ejecutivo'),
(6, 'PQR-152', 'Scania K280', 40, 'Cama'),
-- Quito-Manta (4 buses)
(7, 'STU-153', 'Yutong ZK6122', 40, 'Económico'),
(7, 'STU-154', 'Volvo B9S', 40, 'Ejecutivo'),
(7, 'STU-155', 'Scania K420', 40, 'Cama'),
(7, 'STU-156', 'Mercedes-Benz O500', 40, 'Ejecutivo'),
-- Quito-Ambato (4 buses)
(8, 'VWX-157', 'Hino RN8J', 40, 'Económico'),
(8, 'VWX-158', 'Mercedes-Benz LO915', 40, 'Económico'),
(8, 'VWX-159', 'Volvo B7R', 40, 'Ejecutivo'),
(8, 'VWX-160', 'Scania K280', 40, 'Ejecutivo');

-- Tabla de horarios
CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ruta_id INT NOT NULL,
    hora TIME NOT NULL,
    dias_semana VARCHAR(50) NOT NULL,
    tipo_servicio ENUM('Directo', 'Semi-directo', 'Con escalas') DEFAULT 'Directo',
    FOREIGN KEY (ruta_id) REFERENCES routes(id)
);

-- Insertar más horarios con información detallada
INSERT INTO schedules (ruta_id, hora, dias_semana, tipo_servicio) VALUES
-- Quito-Guayaquil (6 horarios)
(1, '06:00:00', 'Lunes-Domingo', 'Directo'),
(1, '08:00:00', 'Lunes-Domingo', 'Directo'),
(1, '10:00:00', 'Lunes-Domingo', 'Semi-directo'),
(1, '12:00:00', 'Lunes-Domingo', 'Directo'),
(1, '14:00:00', 'Lunes-Domingo', 'Semi-directo'),
(1, '16:00:00', 'Lunes-Domingo', 'Directo'),
(1, '20:00:00', 'Lunes, Miércoles, Viernes', 'Cama'),
(1, '22:00:00', 'Martes, Jueves, Sábado', 'Cama'),
-- Quito-Tulcán (4 horarios)
(2, '07:00:00', 'Lunes-Domingo', 'Directo'),
(2, '09:00:00', 'Lunes-Domingo', 'Directo'),
(2, '13:00:00', 'Lunes-Domingo', 'Semi-directo'),
(2, '15:00:00', 'Lunes-Domingo', 'Directo'),
-- Quito-Loja (4 horarios)
(3, '05:00:00', 'Lunes, Miércoles, Viernes', 'Cama'),
(3, '07:00:00', 'Martes, Jueves, Sábado', 'Cama'),
(3, '15:00:00', 'Lunes-Domingo', 'Directo'),
(3, '17:00:00', 'Lunes-Domingo', 'Semi-directo'),
-- Quito-Lago Agrío (4 horarios)
(4, '06:00:00', 'Lunes-Domingo', 'Directo'),
(4, '08:00:00', 'Lunes-Domingo', 'Directo'),
(4, '16:00:00', 'Lunes-Domingo', 'Semi-directo'),
(4, '18:00:00', 'Lunes-Domingo', 'Directo'),
-- Quito-Esmeraldas (4 horarios)
(5, '07:00:00', 'Lunes-Domingo', 'Directo'),
(5, '09:00:00', 'Lunes-Domingo', 'Semi-directo'),
(5, '11:00:00', 'Lunes-Domingo', 'Directo'),
(5, '17:00:00', 'Lunes-Domingo', 'Directo'),
-- Quito-Cuenca (4 horarios)
(6, '08:00:00', 'Lunes-Domingo', 'Directo'),
(6, '10:00:00', 'Lunes-Domingo', 'Semi-directo'),
(6, '14:00:00', 'Lunes-Domingo', 'Directo'),
(6, '16:00:00', 'Lunes-Domingo', 'Cama'),
-- Quito-Manta (4 horarios)
(7, '09:00:00', 'Lunes-Domingo', 'Directo'),
(7, '11:00:00', 'Lunes-Domingo', 'Semi-directo'),
(7, '13:00:00', 'Lunes-Domingo', 'Directo'),
(7, '15:00:00', 'Lunes-Domingo', 'Directo'),
-- Quito-Ambato (cada hora de 6am a 8pm)
(8, '06:00:00', 'Lunes-Domingo', 'Directo'),
(8, '07:00:00', 'Lunes-Domingo', 'Directo'),
(8, '08:00:00', 'Lunes-Domingo', 'Directo'),
(8, '09:00:00', 'Lunes-Domingo', 'Directo'),
(8, '10:00:00', 'Lunes-Domingo', 'Directo'),
(8, '11:00:00', 'Lunes-Domingo', 'Directo'),
(8, '12:00:00', 'Lunes-Domingo', 'Directo'),
(8, '13:00:00', 'Lunes-Domingo', 'Directo'),
(8, '14:00:00', 'Lunes-Domingo', 'Directo'),
(8, '15:00:00', 'Lunes-Domingo', 'Directo'),
(8, '16:00:00', 'Lunes-Domingo', 'Directo'),
(8, '17:00:00', 'Lunes-Domingo', 'Directo'),
(8, '18:00:00', 'Lunes-Domingo', 'Directo'),
(8, '19:00:00', 'Lunes-Domingo', 'Directo'),
(8, '20:00:00', 'Lunes-Domingo', 'Directo');

-- Tabla de usuarios para autenticación
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(10) UNIQUE NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('normal', 'admin') DEFAULT 'normal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de reservas
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    passenger_id INT NOT NULL,
    schedule_id INT NOT NULL,
    bus_id INT NOT NULL,
    fecha_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'pagado', 'cancelado', 'completado') DEFAULT 'pendiente',
    user_id INT,
    FOREIGN KEY (passenger_id) REFERENCES passengers(id),
    FOREIGN KEY (schedule_id) REFERENCES schedules(id),
    FOREIGN KEY (bus_id) REFERENCES buses(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabla de asientos reservados
CREATE TABLE reservation_seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    bus_id INT NOT NULL,
    fila INT NOT NULL,
    posicion ENUM('ventana_izq', 'ventana_der', 'pasillo_izq', 'pasillo_der') NOT NULL,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id),
    FOREIGN KEY (bus_id) REFERENCES buses(id)
);

-- Tabla de pagos
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    metodo ENUM('transferencia', 'tarjeta', 'efectivo') NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    comprobante VARCHAR(255),
    datos_tarjeta TEXT,
    fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'completado', 'rechazado', 'reembolsado') DEFAULT 'pendiente',
    FOREIGN KEY (reservation_id) REFERENCES reservations(id)
);

-- Tabla de facturas
CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    numero_factura VARCHAR(20) UNIQUE NOT NULL,
    fecha_emision DATETIME DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) NOT NULL,
    iva DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id)
);

-- Tabla de contacto
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    asunto VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('nuevo', 'en_proceso', 'resuelto') DEFAULT 'nuevo'
);

-- Insertar datos de ejemplo para usuarios
-- Contraseñas: password123
INSERT INTO users (cedula, nombre, apellido, email, password, tipo_usuario) VALUES
('1234567890', 'Juan', 'Pérez', 'juan@example.com', '$2y$10$H4.5Dz5b5b5b5b5b5b5b5u5b5b5b5b5b5b5b5b5b5b5b5b5b5b5b', 'normal'),
('0987654321', 'María', 'Gómez', 'maria@example.com', '$2y$10$H4.5Dz5b5b5b5b5b5b5b5u5b5b5b5b5b5b5b5b5b5b5b5b5b5b', 'normal'),
('1111111111', 'Admin', 'Sistema', 'admin@cooperativa.com', '$2y$10$H4.5Dz5b5b5b5b5b5b5b5u5b5b5b5b5b5b5b5b5b5b5b5b5b5b', 'admin');

-- Insertar pasajeros asociados a los usuarios
INSERT INTO passengers (cedula, nombre, apellido, direccion, celular, email) VALUES
('1234567890', 'Juan', 'Pérez', 'Calle Falsa 123, Quito', '0991234567', 'juan@example.com'),
('0987654321', 'María', 'Gómez', 'Avenida Real 456, Guayaquil', '0987654321', 'maria@example.com'),
('1717171717', 'Carlos', 'Rodríguez', 'Boulevard Central 789, Cuenca', '0971717171', 'carlos@example.com'),
('1818181818', 'Ana', 'Martínez', 'Calle Principal 101, Ambato', '0961818181', 'ana@example.com'),
('1919191919', 'Pedro', 'López', 'Avenida de los Shyris 202, Quito', '0951919191', 'pedro@example.com');

-- Insertar algunas reservas de ejemplo
-- Reserva 1: Juan Pérez
SET @schedule_id1 = (SELECT id FROM schedules WHERE ruta_id = 1 LIMIT 1);
SET @bus_id1 = (SELECT id FROM buses WHERE ruta_id = 1 LIMIT 1);
SET @user_id1 = (SELECT id FROM users WHERE cedula = '1234567890');
SET @passenger_id1 = (SELECT id FROM passengers WHERE cedula = '1234567890');

INSERT INTO reservations (passenger_id, schedule_id, bus_id, user_id, estado) 
VALUES (@passenger_id1, @schedule_id1, @bus_id1, @user_id1, 'completado');

SET @reservation_id1 = LAST_INSERT_ID();

INSERT INTO reservation_seats (reservation_id, bus_id, fila, posicion)
VALUES 
(@reservation_id1, @bus_id1, 4, 'ventana_izq'),
(@reservation_id1, @bus_id1, 4, 'pasillo_izq');

INSERT INTO payments (reservation_id, metodo, monto, estado)
VALUES (@reservation_id1, 'tarjeta', 50.00, 'completado');

INSERT INTO invoices (reservation_id, numero_factura, subtotal, iva, total)
VALUES (@reservation_id1, 'FAC-2023-00001', 50.00, 6.00, 56.00);

-- Reserva 2: María Gómez
SET @schedule_id2 = (SELECT id FROM schedules WHERE ruta_id = 2 LIMIT 1);
SET @bus_id2 = (SELECT id FROM buses WHERE ruta_id = 2 LIMIT 1);
SET @user_id2 = (SELECT id FROM users WHERE cedula = '0987654321');
SET @passenger_id2 = (SELECT id FROM passengers WHERE cedula = '0987654321');

INSERT INTO reservations (passenger_id, schedule_id, bus_id, user_id, estado) 
VALUES (@passenger_id2, @schedule_id2, @bus_id2, @user_id2, 'completado');

SET @reservation_id2 = LAST_INSERT_ID();

INSERT INTO reservation_seats (reservation_id, bus_id, fila, posicion)
VALUES 
(@reservation_id2, @bus_id2, 7, 'ventana_der');

INSERT INTO payments (reservation_id, metodo, monto, estado)
VALUES (@reservation_id2, 'transferencia', 15.00, 'completado');

INSERT INTO invoices (reservation_id, numero_factura, subtotal, iva, total)
VALUES (@reservation_id2, 'FAC-2023-00002', 15.00, 1.80, 16.80);

-- Reserva 3: Carlos Rodríguez
SET @schedule_id3 = (SELECT id FROM schedules WHERE ruta_id = 6 LIMIT 1);
SET @bus_id3 = (SELECT id FROM buses WHERE ruta_id = 6 LIMIT 1);
SET @passenger_id3 = (SELECT id FROM passengers WHERE cedula = '1717171717');

INSERT INTO reservations (passenger_id, schedule_id, bus_id, estado) 
VALUES (@passenger_id3, @schedule_id3, @bus_id3, 'completado');

SET @reservation_id3 = LAST_INSERT_ID();

INSERT INTO reservation_seats (reservation_id, bus_id, fila, posicion)
VALUES 
(@reservation_id3, @bus_id3, 3, 'pasillo_der'),
(@reservation_id3, @bus_id3, 3, 'ventana_der');

INSERT INTO payments (reservation_id, metodo, monto, estado)
VALUES (@reservation_id3, 'efectivo', 56.00, 'completado');

INSERT INTO invoices (reservation_id, numero_factura, subtotal, iva, total)
VALUES (@reservation_id3, 'FAC-2023-00003', 56.00, 6.72, 62.72);

-- Reserva 4: Ana Martínez
SET @schedule_id4 = (SELECT id FROM schedules WHERE ruta_id = 8 LIMIT 1);
SET @bus_id4 = (SELECT id FROM buses WHERE ruta_id = 8 LIMIT 1);
SET @passenger_id4 = (SELECT id FROM passengers WHERE cedula = '1818181818');

INSERT INTO reservations (passenger_id, schedule_id, bus_id, estado) 
VALUES (@passenger_id4, @schedule_id4, @bus_id4, 'completado');

SET @reservation_id4 = LAST_INSERT_ID();

INSERT INTO reservation_seats (reservation_id, bus_id, fila, posicion)
VALUES 
(@reservation_id4, @bus_id4, 10, 'pasillo_izq');

INSERT INTO payments (reservation_id, metodo, monto, estado)
VALUES (@reservation_id4, 'tarjeta', 8.00, 'completado');

INSERT INTO invoices (reservation_id, numero_factura, subtotal, iva, total)
VALUES (@reservation_id4, 'FAC-2023-00004', 8.00, 0.96, 8.96);

-- Reserva 5: Pedro López
SET @schedule_id5 = (SELECT id FROM schedules WHERE ruta_id = 3 LIMIT 1);
SET @bus_id5 = (SELECT id FROM buses WHERE ruta_id = 3 LIMIT 1);
SET @passenger_id5 = (SELECT id FROM passengers WHERE cedula = '1919191919');

INSERT INTO reservations (passenger_id, schedule_id, bus_id, estado) 
VALUES (@passenger_id5, @schedule_id5, @bus_id5, 'completado');

SET @reservation_id5 = LAST_INSERT_ID();

INSERT INTO reservation_seats (reservation_id, bus_id, fila, posicion)
VALUES 
(@reservation_id5, @bus_id5, 5, 'ventana_izq');

INSERT INTO payments (reservation_id, metodo, monto, estado)
VALUES (@reservation_id5, 'transferencia', 30.00, 'completado');

INSERT INTO invoices (reservation_id, numero_factura, subtotal, iva, total)
VALUES (@reservation_id5, 'FAC-2023-00005', 30.00, 3.60, 33.60);

-- Insertar mensajes de contacto de ejemplo
INSERT INTO contact_messages (nombre, email, telefono, asunto, mensaje) VALUES
('Luisa Fernández', 'luisa@example.com', '0998765432', 'Consulta sobre equipaje', '¿Cuál es el límite de equipaje permitido en los buses ejecutivos?'),
('Roberto Vargas', 'roberto@example.com', NULL, 'Sugerencia', 'Sería excelente si pudieran agregar wifi en todos sus buses.'),
('Marta Jiménez', 'marta@example.com', '0987123456', 'Problema con reserva', 'No he recibido el correo de confirmación de mi reserva RES-2023-00102');