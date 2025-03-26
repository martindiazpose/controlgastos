<?php
session_start();
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
    $receiptPath = null; // Inicializar la variable para la ruta del comprobante

    // Validar los datos
    if (!empty($tipo) && !empty($categoria) && $monto > 0) {
        // Manejar la subida del archivo si es un egreso
        if ($tipo === 'Egreso' && isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/receipts/';
            $fileName = time() . '_' . basename($_FILES['receipt']['name']); // Evitar nombres duplicados
            $targetFilePath = $uploadDir . $fileName;

            // Crear el directorio si no existe
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Mover el archivo al directorio de destino
            if (move_uploaded_file($_FILES['receipt']['tmp_name'], $targetFilePath)) {
                $receiptPath = $targetFilePath;
            } else {
                throw new Exception("Error al subir el comprobante.");
            }
        }

        // Insertar la transacción en la base de datos
        $query = "INSERT INTO transacciones (tipo, categoria, subcategoria, monto, fecha, paciente, comentarios, comprobante) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparando la consulta: " . $conn->error);
        }

        // Corregir la cadena de tipo para que coincida con todas las variables
        $stmt->bind_param("ssssssss", $tipo, $categoria, $subcategoria, $monto, $fecha, $paciente, $comentarios, $receiptPath);

        if ($stmt->execute()) {
            $response = ['status' => 'success'];

            // Datos del correo
            if (isset($_SESSION['usuario'])) {
                $usuario = $_SESSION['usuario'];
                $detalle = "Tipo: $tipo, Categoría: $categoria, Subcategoría: $subcategoria, Monto: $monto, Comentarios: $comentarios";
                if ($receiptPath) {
                    $detalle .= ", Comprobante: $receiptPath";
                }
                $asunto = "Nueva Transacción Agregada";
                $mensaje = "La transacción ha sido agregada por: $usuario<br>Detalles de la transacción:<br>$detalle";

                // Enviar el correo
                enviarCorreo('martindiazpose@gmail.com', $asunto, $mensaje);
            } else {
                throw new Exception("Usuario no autenticado");
            }
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