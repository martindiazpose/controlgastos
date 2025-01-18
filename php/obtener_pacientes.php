<?php
require 'conexion.php';

$type = $_GET['type'] ?? '';

$response = ['status' => 'error'];

try {
    $patients = [];

    $sql = "SELECT id, nombre FROM pacientes WHERE tipo = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $type);
    if (!$stmt->execute()) {
        throw new Exception("Error en la ejecución de la consulta: " . $stmt->error);
    }

    $stmt->bind_result($id, $nombre);

    while ($stmt->fetch()) {
        $patients[] = ['value' => $id, 'label' => $nombre];
    }

    $response = ['status' => 'success', 'patients' => $patients];
} catch (Exception $e) {
    $response['error'] = 'Error al obtener datos: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>