<?php
require 'conexion.php';


// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$response = ['status' => 'error']; // Respuesta por defecto

switch ($action) {
    case 'add':
        $name = $_POST['name'];
        $type = $_POST['type'];
        $query = "INSERT INTO pacientes (name, type) VALUES ('$name', '$type')";
        if (mysqli_query($connection, $query)) {
            $response = ['status' => 'success'];
        }
        break;
    
    case 'delete':
        $id = $_POST['id'];
        $query = "DELETE FROM pacientes WHERE id = '$id'";
        if (mysqli_query($connection, $query)) {
            $response = ['status' => 'success'];
        }
        break;
    
    case 'list':
        $type = $_GET['type'] ?? 'all';
        if ($type === 'all') {
            $query = "SELECT * FROM pacientes";
        } else {
            $query = "SELECT * FROM pacientes WHERE type = '$type'";
        }
        $result = mysqli_query($connection, $query);
        $patients = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($patients);
        exit;
}

echo json_encode($response);
?>