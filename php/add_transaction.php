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
    $moneda = 'UYU';

    // Validar los datos
    if (!empty($tipo) && !empty($categoria) && $monto > 0) {
        $query = "INSERT INTO transacciones (tipo, categoria, monto, fecha, paciente, comentarios, moneda) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdssss", $tipo, $categoria, $monto, $fecha, $paciente, $comentarios, $moneda);

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
?>