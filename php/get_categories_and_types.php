<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';

$response = ['status' => 'error'];

try {
    $types = [];
    $categories = [];

    // Obtener tipos de movimientos
    $sql = "SELECT DISTINCT tipo AS value, tipo AS label FROM transacciones";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Error en la consulta SQL (tipos): " . $conn->error);
    }
    while ($row = $result->fetch_assoc()) {
        $types[] = $row;
    }

    // Obtener categorías
    $sql = "SELECT DISTINCT categoria AS value, categoria AS label FROM transacciones";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Error en la consulta SQL (categorías): " . $conn->error);
    }
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    $response = ['status' => 'success', 'types' => $types, 'categories' => $categories];
} catch (Exception $e) {
    $response['error'] = 'Error al obtener datos: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>