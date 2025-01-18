<?php
require 'conexion.php';

$type = $_GET['type'] ?? 'all'; // Tipo de transacción (ej. 'Ingreso', 'Egreso')
$month = $_GET['month'] ?? date('Y-m'); // Mes en formato 'YYYY-MM'
$category = $_GET['category'] ?? ''; // Categoría de transacción
$page = $_GET['page'] ?? 1; // Página actual
$limit = 10; // Número de transacciones por página
$offset = ($page - 1) * $limit;

$response = ['status' => 'error'];

try {
    // Escapar parámetros para evitar inyecciones SQL
    $type = $conn->real_escape_string($type);
    $month = $conn->real_escape_string($month);
    $category = $conn->real_escape_string($category);

    // Construir la consulta base
    $sql = "SELECT * FROM transacciones WHERE fecha LIKE '$month%'";

    // Filtrar por tipo si se especifica
    if ($type !== 'all') {
        $sql .= " AND tipo = '$type'";
    }

    // Filtrar por categoría si se especifica
    if (!empty($category)) {
        $sql .= " AND categoria = '$category'";
    }

    // Añadir paginación
    $sql .= " LIMIT $limit OFFSET $offset";

    // Ejecutar la consulta
    $result = $conn->query($sql);

    if ($result) {
        $transactions = [];
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }

        // Contar el total de resultados para la paginación
        $countSql = "SELECT COUNT(*) AS total FROM transacciones WHERE fecha LIKE '$month%'";
        if ($type !== 'all') {
            $countSql .= " AND tipo = '$type'";
        }
        if (!empty($category)) {
            $countSql .= " AND categoria = '$category'";
        }

        $countResult = $conn->query($countSql);
        $total = $countResult->fetch_assoc()['total'];
        $pages = ceil($total / $limit);

        // Respuesta con datos
        $response = [
            'status' => 'success',
            'transactions' => $transactions,
            'page' => $page,
            'pages' => $pages,
            'total' => $total
        ];
    } else {
        // Manejar errores de la consulta
        $response['error'] = $conn->error;
    }
} catch (Exception $e) {
    $response['error'] = 'Error al obtener las transacciones: ' . $e->getMessage();
}

// Devolver la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>