<?php
require 'conexion.php';

header('Content-Type: application/json');

try {
    $type = $_GET['type'] ?? '';

    if (!empty($type)) {
        $stmt = $conn->prepare("SELECT id, nombre FROM categorias WHERE tipo = ?");
        if (!$stmt) {
            throw new Exception("Error preparando la consulta: " . $conn->error);
        }

        $stmt->bind_param('s', $type);
        $stmt->execute();
        $stmt->bind_result($id, $nombre);
        $categories = [];
        while ($stmt->fetch()) {
            $categories[] = ['id' => $id, 'nombre' => $nombre];
        }
        $stmt->close();
    } else {
        $result = $conn->query("SELECT id, nombre, tipo FROM categorias");
        if (!$result) {
            throw new Exception("Error al ejecutar la consulta: " . $conn->error);
        }

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    echo json_encode(['status' => 'success', 'categories' => $categories]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>