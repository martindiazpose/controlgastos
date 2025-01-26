<?php
require 'conexion.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$response = ['status' => 'error'];

try {
    switch ($action) {
        case 'add':
            $nombre = $_POST['name'];
            $tipo = $_POST['type'];
            $query = "INSERT INTO categorias (nombre, tipo) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparing the query: " . $conn->error);
            }
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
            $query = "DELETE FROM categorias WHERE id = ?";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparing the query: " . $conn->error);
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
            $type = $_GET['type'];
            $query = "SELECT id, nombre FROM categorias WHERE tipo = ?";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparing the query: " . $conn->error);
            }
            $stmt->bind_param("s", $type);
            $stmt->execute();

            // Usar bind_result en lugar de get_result
            $stmt->bind_result($id, $nombre);
            $categories = [];
            while ($stmt->fetch()) {
                $categories[] = ['id' => $id, 'nombre' => $nombre];
            }
            echo json_encode($categories);
            exit;

        default:
            $response['error'] = 'Invalid action.';
            break;
    }
} catch (Exception $e) {
    $response['error'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);

$conn->close();
?>