<?php
require 'conexion.php';

header('Content-Type: application/json');

try {
    $type = $_GET['type'] ?? '';

    // Translate type from Spanish to English
    $type_translation = [
        'ingreso' => 'income',
        'egreso' => 'expense'
    ];

    if (!empty($type) && isset($type_translation[$type])) {
        $translated_type = $type_translation[$type];

        $stmt = $conn->prepare("SELECT id, nombre FROM categorias WHERE tipo = ?");
        if (!$stmt) {
            throw new Exception("Error preparando la consulta: " . $conn->error);
        }

        $stmt->bind_param('s', $translated_type);
        $stmt->execute();
        $stmt->bind_result($id, $nombre);
        $categories = [];
        while ($stmt->fetch()) {
            $categories[] = ['id' => $id, 'nombre' => $nombre];
        }
        $stmt->close();
    } else if (empty($type)) {
        $result = $conn->query("SELECT id, nombre, tipo FROM categorias");
        if (!$result) {
            throw new Exception("Error al ejecutar la consulta: " . $conn->error);
        }

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    } else {
        throw new Exception("Tipo no válido: " . $type);
    }

    // Agregar mensaje de depuración
    if (empty($categories)) {
        error_log("No se encontraron categorías para el tipo: " . $type);
    }

    echo json_encode(['status' => 'success', 'categories' => $categories]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>