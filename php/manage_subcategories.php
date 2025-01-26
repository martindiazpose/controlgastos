<?php
require 'conexion.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$response = ['status' => 'error'];

try {
    switch ($action) {
        case 'add':
            $nombre = $_POST['name'];
            $categoria_id = $_POST['category_id'];
            $query = "INSERT INTO subcategorias (nombre, categoria_id) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $conn->error);
            }
            $stmt->bind_param("si", $nombre, $categoria_id);
            if ($stmt->execute()) {
                $response = ['status' => 'success'];
            } else {
                $response['error'] = $stmt->error;
            }
            $stmt->close();
            break;

        case 'delete':
            $id = $_POST['id'];
            $query = "DELETE FROM subcategorias WHERE id = ?";
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
            $type = $_GET['type'];
            $query = "SELECT subcategorias.id, subcategorias.nombre, categorias.nombre AS categoria_nombre 
                      FROM subcategorias 
                      JOIN categorias ON subcategorias.categoria_id = categorias.id 
                      WHERE categorias.tipo = ?";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $conn->error);
            }
            $stmt->bind_param("s", $type);
            $stmt->execute();
            $stmt->bind_result($id, $nombre, $categoria_nombre);
            $subcategories = [];
            while ($stmt->fetch()) {
                $subcategories[] = ['id' => $id, 'nombre' => $nombre, 'categoria_nombre' => $categoria_nombre];
            }
            echo json_encode($subcategories);
            exit;

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