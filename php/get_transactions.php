<?php
require 'conexion.php';

header('Content-Type: application/json');

try {
    $month = $_GET['month'] ?? date('Y-m');
    $type = $_GET['type'] ?? '';
    $category = $_GET['category'] ?? '';
    $page = $_GET['page'] ?? 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    // Actualizar la consulta para obtener nombres de categoría y subcategoría
    $query = "
        SELECT 
            t.id, t.tipo, t.monto, t.fecha, t.paciente, t.comentarios, t.moneda, 
            c.nombre AS categoria_nombre, 
            s.nombre AS subcategoria_nombre
        FROM 
            transacciones t
        LEFT JOIN 
            categorias c ON t.categoria = c.id
        LEFT JOIN 
            subcategorias s ON t.subcategoria = s.id
        WHERE 
            DATE_FORMAT(t.fecha, '%Y-%m') = ?
    ";

    $params = [$month];
    $types = 's';

    if (!empty($type)) {
        $query .= " AND t.tipo = ?";
        $types .= 's';
        $params[] = $type;
    }

    if (!empty($category)) {
        $query .= " AND t.categoria = ?";
        $types .= 's';
        $params[] = $category;
    }

    $query .= " LIMIT ? OFFSET ?";
    $types .= 'ii';
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Error preparando la consulta: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->bind_result($id, $tipo, $monto, $fecha, $paciente, $comentarios, $moneda, $categoriaNombre, $subcategoriaNombre);
    
    $transactions = [];
    while ($stmt->fetch()) {
        $transactions[] = [
            'id' => $id,
            'tipo' => $tipo,
            'categoria' => $categoriaNombre,
            'subcategoria' => $subcategoriaNombre,
            'monto' => $monto,
            'fecha' => $fecha,
            'paciente' => $paciente,
            'comentarios' => $comentarios,
            'moneda' => $moneda
        ];
    }

    // Obtener el número total de transacciones
    $total_query = "SELECT COUNT(*) as total FROM transacciones t 
                    WHERE DATE_FORMAT(t.fecha, '%Y-%m') = ?";
    $params = [$month];
    $types = 's';

    if (!empty($type)) {
        $total_query .= " AND t.tipo = ?";
        $types .= 's';
        $params[] = $type;
    }

    if (!empty($category)) {
        $total_query .= " AND t.categoria = ?";
        $types .= 's';
        $params[] = $category;
    }

    $total_stmt = $conn->prepare($total_query);
    if (!$total_stmt) {
        throw new Exception("Error preparando la consulta: " . $conn->error);
    }

    $total_stmt->bind_param($types, ...$params);
    $total_stmt->execute();
    $total_stmt->bind_result($total_transactions);
    $total_stmt->fetch();
    $total_pages = ceil($total_transactions / $limit);

    echo json_encode([
        'status' => 'success',
        'transactions' => $transactions,
        'page' => $page,
        'pages' => $total_pages
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>