<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // No cifrar la contraseña aquí

    echo "Usuario: $username, Contraseña: " . htmlspecialchars($password); // Verificar que se recibieron correctamente

    $stmt = $conn->prepare('SELECT id, username, password FROM usuarios WHERE username = ?');
    if (!$stmt) {
        echo "Error preparando la consulta: " . $conn->error;
        exit;
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($id, $db_username, $db_password_hash);

    if ($stmt->fetch() && password_verify($password, $db_password_hash)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $db_username;
        echo "Inicio de sesión exitoso. Redirigiendo...";
        header('Location: ../controlgastos.html');
        exit;
    } else {
        echo "Error: Nombre de usuario o contraseña incorrectos.";
        header('Location: /login.html?error=1');
        exit;
    }

    $stmt->close();
}

$conn->close();
?>