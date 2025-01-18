<?php
require 'conexion.php';

$response = ['status' => 'error'];

try {
    $tipo = $_POST['type'] ?? '';
    $categoria_id = $_POST['category'] ?? 0;
    $monto = $_POST['amount'] ?? 0;
    $fecha = date('Y-m-d');
    $paciente_id = $_POST['patient'] ?? null;
    $comentarios = $_POST['comments'] ?? '';

    // Validar los datos
    if (!empty($tipo) && $categoria_id > 0 && $monto > 0) {
        $query = "INSERT INTO transacciones (tipo, categoria_id, monto, fecha, paciente_id, comentarios) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sidiss", $tipo, $categoria_id, $monto, $fecha, $paciente_id, $comentarios);

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