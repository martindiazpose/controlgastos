<?php
require 'conexion.php';

$response = ['status' => 'error'];

try {
    $query = "SELECT * FROM categorias";
    $result = $conn->query($query);

    if ($result) {
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = [
                'id' => $row['id'],
                'nombre' => $row['nombre'],
                'tipo' => $row['tipo']
            ];
        }
        $response = ['status' => 'success', 'categories' => $categories];
    } else {
        $response['error'] = 'Error al obtener las categorías: ' . $conn->error;
    }
} catch (Exception $e) {
    $response['error'] = 'Error al obtener las categorías: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>