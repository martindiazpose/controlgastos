<?php
require 'conexion.php';

$response = ['status' => 'error'];

try {
    $tipo = $_POST['type'] ?? '';
    $categoria = $_POST['category'] ?? '';
    $monto = $_POST['amount'] ?? 0;
    $fecha = date('Y-m-d');
    $paciente = $_POST['patient'] ?? null;
    $comentarios = $_POST['comments'] ?? '';

    // Validar los datos
    if (!empty($tipo) && !empty($categoria) && $monto > 0) {
        $query = "INSERT INTO transacciones (tipo, categoria, monto, fecha, paciente, comentarios) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparando la consulta: " . $conn->error);
        }

        $stmt->bind_param("ssds", $tipo, $categoria, $monto, $fecha, $paciente, $comentarios);

        if ($stmt->execute()) {
            $response = ['status' => 'success'];
        } else {
            $response['error'] = $stmt->error;
        }

        $stmt->close();
    } else {
        $response['error'] = 'Datos inválidos';
    }
} catch (Exception $e) {
    $response['error'] = 'Error al agregar la transacción: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>