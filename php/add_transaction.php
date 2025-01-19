<?php
require 'conexion.php';

$response = ['status' => 'error'];

try {
    $tipo = $_POST['type'] ?? '';
    $categoria = $_POST['category'] ?? '';
    $monto = $_POST['amount'] ?? 0;
    $fecha = date('Y-m-d'); // Tomar la fecha actual
    $paciente = $_POST['patient'] ?? null;
    $comentarios = $_POST['comments'] ?? '';

    // Agregar depuración para verificar la fecha
    error_log("Fecha asignada: " . $fecha);

    // Validar los datos
    if (!empty($tipo) && !empty($categoria) && $monto > 0) {
        $query = "INSERT INTO transacciones (tipo, categoria, monto, fecha, paciente, comentarios) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparando la consulta: " . $conn->error);
        }

        // Corregir la cadena de tipo para que coincida con todas las variables
        $stmt->bind_param("ssssss", $tipo, $categoria, $monto, $fecha, $paciente, $comentarios);

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