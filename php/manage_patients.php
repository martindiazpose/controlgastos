<?php
header('Content-Type: application/json'); // Asegúrate de que la respuesta sea JSON
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'conexion.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$response = ['status' => 'error'];

try {
    switch ($action) {
        case 'add':
            $nombre = $_POST['name'] ?? '';
            $tipo = $_POST['type'] ?? '';

            if (empty($nombre) || empty($tipo)) {
                echo json_encode(['status' => 'error', 'error' => 'Campos incompletos']);
                exit;
            }

            $query = "INSERT INTO pacientes (nombre, tipo) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                echo json_encode(['status' => 'error', 'error' => $conn->error]);
                exit;
            }

            $stmt->bind_param("ss", $nombre, $tipo);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'error' => $stmt->error]);
            }
            $stmt->close();
            exit;

        case 'delete':
            $id = $_POST['id'];
            $query = "DELETE FROM pacientes WHERE id = ?";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $conn->error);
            }
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

        default:
            $response['error'] = 'Acción no válida.';
            break;
    }
} catch (Exception $e) {
    $response['error'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);

$conn->close();
?>