<?php
header('Content-Type: application/json');
require 'conexion.php';

$response = ['status' => 'error'];

try {
    $query = "
        SELECT 
            t.id,
            t.tipo,
            c.nombre AS categoria_nombre,
            s.nombre AS subcategoria_nombre,
            t.monto,
            t.fecha,
            t.paciente,
            t.comentarios,
            t.moneda,
            t.comprobante
        FROM 
            transacciones t
        LEFT JOIN 
            categorias c ON t.categoria = c.id
        LEFT JOIN 
            subcategorias s ON t.subcategoria = s.id
        ORDER BY t.id DESC"; // Ordenar por ID en orden descendente (último agregado primero)

    $result = $conn->query($query);

    if ($result) {
        $transactions = [];
        while ($row = $result->fetch_assoc()) {
            // Verifica si el comprobante tiene un valor válido
            $comprobantePath = !empty($row['comprobante']) && $row['comprobante'] !== 'null'
                ? 'https://controlgastos.wantedstudio.uy/' . ltrim($row['comprobante'], '/')
                : null;

            $transactions[] = [
                'id' => $row['id'],
                'tipo' => $row['tipo'],
                'categoria_nombre' => $row['categoria_nombre'],
                'subcategoria' => $row['subcategoria_nombre'],
                'monto' => $row['monto'],
                'fecha' => $row['fecha'],
                'paciente' => $row['paciente'],
                'comentarios' => $row['comentarios'],
                'comprobante' => $comprobantePath, // Ruta ajustada o null
            ];
        }
        $response = ['status' => 'success', 'transactions' => $transactions];
    } else {
        $response['error'] = 'Error al ejecutar la consulta.';
    }
} catch (Exception $e) {
    $response['error'] = 'Error del servidor: ' . $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>