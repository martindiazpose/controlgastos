<?php
header('Content-Type: application/json');

// Verifica si se recibió el ID
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if ($id) {
        // Incluir el archivo de conexión
        include 'conexion.php';

        // Verifica la conexión
        if ($conn->connect_error) {
            echo json_encode(['status' => 'error', 'error' => 'Error de conexión a la base de datos']);
            exit;
        }

        // Elimina la transacción
        $stmt = $conn->prepare('DELETE FROM transacciones WHERE id = ?');
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'error' => 'No se pudo eliminar la transacción']);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'error' => 'ID no proporcionado']);
    }
} else {
    echo json_encode(['status' => 'error', 'error' => 'Método no permitido']);
}
?>