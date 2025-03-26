<?php
// Verifica si la sesión ya está activa antes de iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica si el usuario está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirige al login si no está autenticado
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finanzas Personales</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Enlace al archivo CSS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" href="/assets/img/carpeta.png" type="image/x-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="container">
    <!-- Título principal -->
    <header class="text-center my-4">
        <h1>Control de Finanzas</h1>
        <!-- Botón de Perfil con menú desplegable -->
        <div class="dropdown text-right">
            <button class="btn btn-secondary dropbtn">Perfil</button>
            <div class="dropdown-content">
                <a href="assets/pages/perfil.php">Perfil</a>
                <a href="assets/pages/admin_panel.php">Adm Usuario</a>
                <a href="assets/pages/dashboard.php">Dashboard</a>
                <a href="php/logout.php" class="text-danger">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
            </div>
        </div>
    </header>

        <!-- Modal -->
        <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Mensaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalMessage">
                    <!-- Mensaje se mostrará aquí -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Sección: Agregar transacciones -->
    <section id="add-transaction" class="mb-5">
        <div class="card shadow-sm" style="border: 1px solid #d1d1d1; border-radius: 10px; background-color: #f9f9f9;">
            <div class="card-header text-center" style="background-color: #a3d5f7; color:rgb(8, 8, 8); border-radius: 10px 10px 0 0;">
                <h2 class="mb-0">Agregar Transacciones</h2>
                <p class="mb-0">Registra tus ingresos y egresos fácilmente</p>
            </div>
            <div class="card-body">
                <form id="finance-form" class="form-inline mb-4" enctype="multipart/form-data" method="POST" action="php/add_transaction.php">
                    <div class="form-group mr-2">
                        <label for="type" class="mr-2"><i class="fas fa-exchange-alt"></i> Tipo:</label>
                        <select id="type" name="type" class="form-control" required>
                            <option value="">Seleccionar</option>
                            <option value="Ingreso">Ingreso</option>
                            <option value="Egreso">Egreso</option>
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <label for="category" class="mr-2"><i class="fas fa-list"></i> Categoría:</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="">Seleccionar</option>
                            <!-- Opciones serán cargadas dinámicamente -->
                        </select>
                    </div>
                    <div id="subcategory-field" class="form-group mr-2" style="display: none;">
                        <label for="subcategory" class="mr-2"><i class="fas fa-tags"></i> Subcategoría:</label>
                        <select id="subcategory" name="subcategory" class="form-control"></select>
                    </div>
                    <div id="patient-field" class="form-group mr-2" style="display: none;">
                        <label for="patient" class="mr-2"><i class="fas fa-user"></i> Paciente:</label>
                        <select id="patient" name="patient" class="form-control"></select>
                    </div>
                    <div class="form-group mr-2">
                        <label for="amount" class="mr-2"><i class="fas fa-dollar-sign"></i> Monto:</label>
                        <input type="number" id="amount" name="amount" step="0.01" class="form-control" required>
                    </div>
                    <div class="form-group mr-2">
                        <label for="comments" class="mr-2"><i class="fas fa-comment"></i> Comentarios:</label>
                        <input type="text" id="comments" name="comments" class="form-control">
                    </div>
                    <div id="receipt-field" class="form-group mr-2" style="display: none;">
                        <label for="receipt" class="mr-2"><i class="fas fa-file-alt"></i> Comprobante de Pago:</label>
                        <input type="file" id="receipt" name="receipt" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success" style="background-color: #a3d5f7; border-color: #a3d5f7; color: rgb(8, 8, 8);">
                        <i class="fas fa-plus"></i> Agregar
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Botones de navegación -->
    <section id="navigation-buttons" class="text-center mt-5">
        <div class="btn-group" role="group" aria-label="Navegación">
            <a href="assets/pages/perfil.php" class="btn" style="background-color: #f4b942; color: rgb(8, 8, 8); border-color: #f4b942;">
                <i class="fas fa-user"></i> Perfil
            </a>
            <a href="assets/pages/admin_panel.php" class="btn" style="background-color: #e74c3c; color: rgb(8, 8, 8); border-color: #e74c3c;">
                <i class="fas fa-users-cog"></i> Admin Panel
            </a>
            <a href="assets/pages/dashboard.php" class="btn" style="background-color: #3498db; color:rgb(8, 8, 8); border-color: #3498db;">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </div>
    </section>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
</body>
</html>
<script src="assets/js/scripts.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>