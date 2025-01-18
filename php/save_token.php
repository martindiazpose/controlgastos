<?php
header("Content-Type: application/json");
$mysqli = new mysqli("localhost", "usuario", "contraseña", "base_datos");

// Manejar errores de conexión
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión con la base de datos"]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Obtener pacientes
        $tipo = $_GET['tipo'] ?? '';
        if (!in_array($tipo, ['mensual', 'semanal'])) {
            http_response_code(400);
            echo json_encode(["error" => "Tipo inválido"]);
            exit();
        }
        $stmt = $mysqli->prepare("SELECT id, nombre FROM pacientes WHERE tipo = ?");
        $stmt->bind_param("s", $tipo);
        $stmt->execute();
        $result = $stmt->get_result();
        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        break;

    case 'POST':
        // Agregar paciente
        $data = json_decode(file_get_contents("php://input"), true);
        $nombre = $data['nombre'] ?? '';
        $tipo = $data['tipo'] ?? '';
        if (!$nombre || !in_array($tipo, ['mensual', 'semanal'])) {
            http_response_code(400);
            echo json_encode(["error" => "Datos inválidos"]);
            exit();
        }
        $stmt = $mysqli->prepare("INSERT INTO pacientes (nombre, tipo) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $tipo);
        if ($stmt->execute()) {
            echo json_encode(["id" => $mysqli->insert_id, "nombre" => $nombre, "tipo" => $tipo]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al insertar"]);
        }
        break;

    case 'DELETE':
        // Eliminar paciente
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID inválido"]);
            exit();
        }
        $stmt = $mysqli->prepare("DELETE FROM pacientes WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}

$mysqli->close();
?>
