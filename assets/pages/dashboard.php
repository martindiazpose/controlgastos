
<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirige al login si no está autenticado
    header("Location: ../../login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Carga de Chart.js -->
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/scripts.js" defer></script> <!-- Carga de scripts.js -->
</head>

<body class="container">
    <h1 class="text-center my-4">Estado de Cuenta</h1>
    <div class="dropdown text-right">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown"
            aria-expanded="false">
            Perfil
        </button>
        <ul class="dropdown-menu" aria-labelledby="profileDropdown">
            <li><a class="dropdown-item" href="../../index.php">Inicio</a></li>
            <li><a class="dropdown-item" href="./admin_panel.php">Adm Usuario</a></li>
            <li><a class="dropdown-item text-danger" href="../../php/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a></li>
        </ul>
    </div>

    <!-- Sección: Balance -->
    <section id="balance-info" class="mb-5">
        <h2 class="text-center mb-4">Balance</h2>
        <div class="balance-info">
            <div class="income">
                <h4>Ingresos</h4>
                <p id="total-income">--</p>
            </div>
            <div class="expense">
                <h4>Egresos</h4>
                <p id="total-expense">--</p>
            </div>
            <div class="balance">
                <h4>Balance</h4>
                <p id="total-balance">--</p>
            </div>
            <div class="savings">
                <h4>Ahorros</h4>
                <p id="total-savings">--</p>
                <p id="total-savings-usd">--</p>
            </div>
        </div>
    </section>



    <!-- Filtro por mes -->
    <div class="form-group">
        <label for="filter-month">Filtrar por mes:</label>
        <input type="month" id="filter-month" class="form-control">
    </div>

    <!-- Tabla de transacciones -->
    <div class="table-responsive">
        <table id="transactions-table" class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Categoría</th>
                    <th>Subcategoría</th>
                    <th>Monto</th>
                    <th>Comentarios</th>
                    <th>Paciente</th>
                    <th>Acciones</th>
                    <th>Comprobante</th>
                </tr>
            </thead>
            <tbody>
                <!-- Las filas se cargarán dinámicamente con JavaScript -->
            </tbody>
        </table>
        <!-- Controles de paginación -->
        <nav>
            <ul id="pagination-controls" class="pagination justify-content-center">
                <!-- Los botones de paginación se generarán dinámicamente -->
            </ul>
        </nav>
    </div>

    <!-- Modal para ver detalles de la transacción -->
    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionModalLabel">Detalles de la Transacción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>ID:</strong> <span id="modal-transaction-id"></span></p>
                    <p><strong>Fecha:</strong> <span id="modal-transaction-date"></span></p>
                    <p><strong>Tipo:</strong> <span id="modal-transaction-type"></span></p>
                    <p><strong>Categoría:</strong> <span id="modal-transaction-category"></span></p>
                    <p><strong>Monto:</strong> <span id="modal-transaction-amount"></span></p>
                    <p><strong>Comentarios:</strong> <span id="modal-transaction-comments"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar eliminación -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar esta transacción?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Gráficos -->
    <section id="charts" class="mb-5">
        <h2 class="text-center mb-4">Gráficos</h2>
        <div class="chart-container">
            <canvas id="income-expense-chart" class="animated-chart" width="400" height="400"></canvas>
            <canvas id="expense-category-chart" class="animated-chart" width="400" height="400"></canvas>
            <canvas id="income-category-chart" class="animated-chart" width="400" height="400"></canvas>
            <canvas id="subcategory-gastos-fijos-chart" class="animated-chart" width="400" height="400"></canvas>
            <canvas id="category-comida-chart" class="animated-chart" width="400" height="400"></canvas>
        </div>
    </section>
</body>

</html>