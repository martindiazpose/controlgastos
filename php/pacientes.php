
<?php
include 'conexion.php';

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $stmt = $conn->prepare("INSERT INTO patients (name, type) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $type);
    $stmt->execute();
    $stmt->close();
} elseif ($action === 'delete') {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
} elseif ($action === 'list') {
    $result = $conn->query("SELECT * FROM patients");
    $patients = [];
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
    echo json_encode($patients);
}

$conn->close();
?>