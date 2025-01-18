<?php
require 'conexion.php';

header('Content-Type: application/json');

try {
    $type = $_GET['type'] ?? '';

    if (empty($type)) {
        throw new Exception("El tipo es requerido.");
    }

    $stmt = $conn->prepare("SELECT id, nombre FROM pacientes WHERE tipo = ?");
    if (!$stmt) {
        throw new Exception("Error preparando la consulta: " . $conn->error);
    }

    $stmt->bind_param('s', $type);
    $stmt->execute();
    $result = $stmt->get_result();

    $patients = [];
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }

    echo json_encode(['status' => 'success', 'patients' => $patients]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>