<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirige al login si no está autenticado
    header("Location: ../../login.html");
    exit();
}
?>