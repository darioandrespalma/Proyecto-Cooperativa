<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $direccion = $_POST['direccion'];
    $celular = $_POST['celular'];
    $email = $_POST['email'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO passengers (cedula, nombre, apellido, direccion, celular, email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$cedula, $nombre, $apellido, $direccion, $celular, $email]);
        
        $_SESSION['passenger_id'] = $pdo->lastInsertId();
        header('Location: ../routes.php');
        exit;
    } catch (PDOException $e) {
        $error = "Error al registrar: " . $e->getMessage();
    }
}
?>