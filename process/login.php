<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $cedula = $_POST['cedula'];

    $stmt = $pdo->prepare("SELECT * FROM passengers WHERE cedula = ? OR email = ?");
    $stmt->execute([$cedula, $email]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['passenger_id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre']; // opcional para mostrar nombre
        header('Location: ../routes.php'); // o donde empiece el proceso de reserva
        exit;
    } else {
        $_SESSION['login_error'] = "Usuario no encontrado. Verifique su cédula o email.";
        header('Location: ../login.php');
        exit;
    }
}
?>

