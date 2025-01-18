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

    $query = "SELECT t.*, c.nombre as categoria, p.nombre as paciente 
              FROM transacciones t 
              LEFT JOIN categorias c ON t.categoria_id = c.id
              LEFT JOIN pacientes p ON t.paciente_id = p.id
              WHERE DATE_FORMAT(t.fecha, '%Y-%m') = ?";

    $params = [$month];
    $types = 's';

    if (!empty($type)) {
        $query .= " AND t.tipo = ?";
        $types .= 's';
        $params[] = $type;
    }

    if (!empty($category)) {
        $query .= " AND c.nombre = ?";
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
    $result = $stmt->get_result();
    $transactions = $result->fetch_all(MYSQLI_ASSOC);

    // Obtener el número total de transacciones
    $total_query = "SELECT COUNT(*) as total FROM transacciones t 
                    LEFT JOIN categorias c ON t.categoria_id = c.id
                    WHERE DATE_FORMAT(t.fecha, '%Y-%m') = ?";
    $params = [$month];
    $types = 's';

    if (!empty($type)) {
        $total_query .= " AND t.tipo = ?";
        $types .= 's';
        $params[] = $type;
    }

    if (!empty($category)) {
        $total_query .= " AND c.nombre = ?";
        $types .= 's';
        $params[] = $category;
    }

    $total_stmt = $conn->prepare($total_query);
    if (!$total_stmt) {
        throw new Exception("Error preparando la consulta: " . $conn->error);
    }

    $total_stmt->bind_param($types, ...$params);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result();
    $total_transactions = $total_result->fetch_assoc()['total'];
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