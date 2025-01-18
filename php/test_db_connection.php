<?php
// Mostrar errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Datos de conexión
$servername = "sql212.infinityfree.com";
$username = "if0_37060924";
$password = "9Wcq2GjC3Vc";
$dbname = "if0_37060924_finanzas_personales";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

// Probar una consulta simple
$sql = "SELECT * FROM transacciones LIMIT 1";
$result = $conn->query($sql);

if ($result) {
    echo "<br>Query executed successfully. Number of rows: " . $result->num_rows;
} else {
    echo "<br>Error executing query: " . $conn->error;
}

$conn->close();
?>
