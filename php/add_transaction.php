<?php
require 'conexion.php';
require 'send_email.php'; // Incluir el archivo de la función de envío de correos

$response = ['status' => 'error'];

try {
    $tipo = $_POST['type'] ?? '';
    $categoria = $_POST['category'] ?? '';
    $subcategoria = $_POST['subcategory'] ?? null;
    $monto = $_POST['amount'] ?? 0;
    $fecha = date('Y-m-d'); // Tomar la fecha actual
    $paciente = $_POST['patient'] ?? null;
    $comentarios = $_POST['comments'] ?? '';

    // Validar los datos
    if (!empty($tipo) && !empty($categoria) && $monto > 0) {
        $query = "INSERT INTO transacciones (tipo, categoria, subcategoria, monto, fecha, paciente, comentarios) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparando la consulta: " . $conn->error);
        }

        // Corregir la cadena de tipo para que coincida con todas las variables
        $stmt->bind_param("sssssss", $tipo, $categoria, $subcategoria, $monto, $fecha, $paciente, $comentarios);

        if ($stmt->execute()) {
            $response = ['status' => 'success'];

            // Datos del correo
            $usuario = $_SESSION['usuario']; // Asegúrate de tener la sesión iniciada y el nombre de usuario disponible
            $detalle = "Tipo: $tipo, Categoría: $categoria, Subcategoría: $subcategoria, Monto: $monto, Comentarios: $comentarios";
            $asunto = "Nueva Transacción Agregada";
            $mensaje = "La transacción ha sido agregada por: $usuario<br>Detalles de la transacción:<br>$detalle";

            // Enviar el correo
            enviarCorreo('martindiazpose@gmail.com', $asunto, $mensaje);
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