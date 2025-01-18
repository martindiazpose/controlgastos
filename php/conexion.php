<?php
    $servername = "localhost";
    $username = "wantedst_admin";
    $password = "Facil.2024***";
$dbname = "wantedst_finanzas_personales";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
