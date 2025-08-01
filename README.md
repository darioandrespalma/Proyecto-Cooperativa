# Sistema de Reserva de Autobuses - Cooperativa de Transporte

## Instalación
1. Instalar XAMPP (https://www.apachefriends.org/)
2. Clonar este repositorio en `xampp/htdocs/proyecto_cooperativa`
3. Importar la base de datos:
   - Acceder a phpMyAdmin (http://localhost/phpmyadmin)
   - Crear nueva base de datos: `cooperativa_transporte`
   - Importar `database/db.sql`
4. Configurar si es necesario en `includes/db_connect.php`
5. Acceder a http://localhost/proyecto_cooperativa/

## Tecnologías utilizadas
- PHP 7.4+
- MySQL 5.7+
- HTML5
- CSS3
- JavaScript (ES6)
- XAMPP (Entorno de desarrollo)

## Características de usabilidad implementadas
1. Diseño responsivo
2. Retroalimentación inmediata en formularios
3. Progreso visual del proceso
4. Confirmación antes de acciones críticas
5. Mensajes de error claros
6. Tamaño de fuente adaptable
7. Alto contraste (modo accesibilidad)
8. Navegación consistente
9. Carga progresiva
10. Indicadores de estado

## Características de accesibilidad implementadas
1. Navegación por teclado completa
2. Etiquetas ARIA
3. Texto alternativo para imágenes
4. Alto contraste
5. Tamaño de texto escalable
6. Enfoque visible
7. Encabezados semánticos
8. Atajos de teclado (Ctrl+S para contenido)
9. Regiones ARIA live para actualizaciones
10. Validación accesible de formularios

## Proceso de reserva
1. Registro de pasajero
2. Selección de ruta
3. Selección de horario y bus
4. Selección de asientos
5. Proceso de pago
6. Confirmación y factura

## Pruebas de portabilidad
El sistema ha sido probado en:
- Windows 10 con XAMPP
- Ubuntu 20.04 con LAMP
- macOS Big Sur con MAMP