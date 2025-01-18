<?php
require 'conexion.php';

$type = $_GET['type'] ?? '';
if ($type === 'mensual' || $type === 'semanal') {
    $stmt = $conn->prepare("SELECT id, nombre FROM pacientes WHERE tipo = ?");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $patients = $result->fetch_all(MYSQLI_ASSOC);

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'patients' => $patients]);
} else {
    echo json_encode(['status' => 'error', 'error' => 'Tipo inválido']);
}

$conn->close();
?>