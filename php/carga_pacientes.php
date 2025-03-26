<?php
require 'conexion.php';

// Crear conexión
$connection = new mysqli($servername, $username, $password, $dbname);

if ($connection->connect_error) {
    die(json_encode(['status' => 'error', 'error' => 'Connection failed: ' . $connection->connect_error]));
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$response = ['status' => 'error', 'error' => 'Unknown error']; // Respuesta por defecto

switch ($action) {
    case 'add':
        $name = $_POST['name'];
        $type = $_POST['type'];
        $query = "INSERT INTO pacientes (name, type) VALUES ('$name', '$type')";
        if (mysqli_query($connection, $query)) {
            $response = ['status' => 'success'];
        } else {
            $response = ['status' => 'error', 'error' => 'Error adding patient: ' . $connection->error];
        }
        break;
    
    case 'delete':
        $id = $_POST['id'];
        $query = "DELETE FROM pacientes WHERE id = '$id'";
        if (mysqli_query($connection, $query)) {
            $response = ['status' => 'success'];
        } else {
            $response = ['status' => 'error', 'error' => 'Error deleting patient: ' . $connection->error];
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
        if ($result) {
            $patients = mysqli_fetch_all($result, MYSQLI_ASSOC);
            echo json_encode(['status' => 'success', 'patients' => $patients]);
        } else {
            $response = ['status' => 'error', 'error' => 'Error fetching patients: ' . $connection->error];
            echo json_encode($response);
        }
        exit;
}

echo json_encode($response);
?>