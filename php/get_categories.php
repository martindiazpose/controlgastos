<?php
require 'conexion.php';

header('Content-Type: application/json');

try {
    $result = $conn->query("SELECT id, nombre FROM categorias");
    if (!$result) {
        throw new Exception("Error al ejecutar la consulta: " . $conn->error);
    }

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    echo json_encode(['status' => 'success', 'categories' => $categories]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>