<?php
require_once '../includes/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $direccion = $_POST['direccion'] ?? null;
    $celular = $_POST['celular'] ?? null;
    $email = $_POST['email'];

    try {
        $stmt = $pdo->prepare("INSERT INTO passengers (cedula, nombre, apellido, direccion, celular, email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$cedula, $nombre, $apellido, $direccion, $celular, $email]);

        // Luego de registrar, redirige al login sin iniciar sesión
        $_SESSION['success'] = "Registro exitoso. Ahora puede iniciar sesión.";
        header("Location: ../login.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['register_error'] = "Error al registrar: " . $e->getMessage();
        header("Location: /register.php");
        exit();
    }
}
?>
