<?php
require 'conexion.php';

header('Content-Type: application/json');

$category_id = $_GET['category_id'] ?? '';

$response = ['status' => 'error'];

if ($category_id) {
    try {
        $query = "SELECT id, nombre FROM subcategorias WHERE categoria_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();

        // Usar bind_result en lugar de get_result
        $stmt->bind_result($id, $nombre);
        $subcategories = [];
        while ($stmt->fetch()) {
            $subcategories[] = ['id' => $id, 'nombre' => $nombre];
        }

        $response = ['status' => 'success', 'subcategories' => $subcategories];
    } catch (Exception $e) {
        $response['error'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['error'] = 'Invalid category ID.';
}

echo json_encode($response);

$conn->close();
?>