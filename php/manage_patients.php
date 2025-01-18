<?php
require 'conexion.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$response = ['status' => 'error'];

switch ($action) {
    case 'add':
        $nombre = $_POST['name'];
        $tipo = $_POST['type'];
        $query = "INSERT INTO pacientes (nombre, tipo) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $nombre, $tipo);
        if ($stmt->execute()) {
            $response = ['status' => 'success'];
        } else {
            $response['error'] = $stmt->error;
        }
        $stmt->close();
        break;

    case 'delete':
        $id = $_POST['id'];
        $query = "DELETE FROM pacientes WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $response = ['status' => 'success'];
        } else {
            $response['error'] = $stmt->error;
        }
        $stmt->close();
        break;

    case 'list':
        $query = "SELECT * FROM pacientes";
        $result = $conn->query($query);
        if ($result) {
            $patients = [];
            while ($row = $result->fetch_assoc()) {
                $patients[] = $row;
            }
            echo json_encode($patients);
            exit;
        } else {
            $response['error'] = $conn->error;
        }
        break;
}

echo json_encode($response);
?>