<?php
require 'conexion.php';

header('Content-Type: application/json');

try {
    $type = $_GET['type'] ?? '';

    if (empty($type)) {
        throw new Exception("El tipo es requerido.");
    }

    // Agregar mensajes de depuración para verificar el tipo recibido
    error_log("Tipo recibido: " . $type);

    $stmt = $conn->prepare("SELECT id, nombre FROM pacientes WHERE tipo = ?");
    if (!$stmt) {
        throw new Exception("Error preparando la consulta: " . $conn->error);
    }

    $stmt->bind_param('s', $type);
    $stmt->execute();
    $stmt->bind_result($id, $nombre);

    $patients = [];
    while ($stmt->fetch()) {
        $patients[] = ['id' => $id, 'nombre' => $nombre];
    }

    echo json_encode(['status' => 'success', 'patients' => $patients]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
?>