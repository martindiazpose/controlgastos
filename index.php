<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'php/session_check.php'; ?>
    <link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finanzas Personales</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" href="assets/img/carpeta.png" type="image/x-icon">
    <style>
        #patient-field {
            display: none;
        }
        .chart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 20px;
        }
        .chart-container canvas {
            width: 100%;
            max-width: 400px;
            height: auto;
            max-height: 400px;
        }
        @media (min-width: 768px) {
            .chart-container canvas {
                max-width: 45%;
            }
        }
        .balance-info {
            display: flex;
            flex-direction: column;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .balance-info div {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
        }
        .balance-info .income {
            background-color: #d4edda;
            color: #155724;
        }
        .balance-info .expense {
            background-color: #f8d7da;
            color: #721c24;
        }
        .balance-info .balance {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .balance-info .savings {
            background-color: #fff3cd;
            color: #856404;
        }
        @media (min-width: 768px) {
            .balance-info {
                flex-direction: row;
                flex-wrap: wrap;
            }
            .balance-info div {
                flex: 1 1 200px;
                margin: 5px;
            }
            .chart-container {
                justify-content: space-between;
            }
            .chart-container canvas {
                max-width: 32%;
            }
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f6d365;
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 15px;
        }
        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-weight: bold;
        }
        .dropdown-content a:hover {
            background-color: #ff7e5f;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .dropdown:hover .dropbtn {
            background-color: #feb47b;
        }
        .modal-content {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            border-radius: 15px;
            color: #fff;
            text-align: center;
            font-family: 'Arial', sans-serif;
            transform: scale(0.7);
            animation: modalScale 0.3s forwards;
        }
        .modal-header,
        .modal-footer {
            border: none;
        }
        .modal-title {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .modal-body {
            font-size: 1.2rem;
            padding: 20px;
        }
        .btn-close {
            color: #fff;
            opacity: 1;
            background-color: #ff7e5f;
            border-radius: 50%;
            border: none;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-close:hover {
            background-color: #feb47b;
        }
        .btn-primary {
            background-color: #ff7e5f;
            border-color: #ff7e5f;
        }
        .btn-primary:hover {
            background-color: #feb47b;
            border-color: #feb47b;
        }
        @keyframes modalScale {
            from {
                transform: scale(0.7);
            }
            to {
                transform: scale(1);
            }
        }
    </style>
</head>
<body class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="text-center w-100">Finanzas Personales</h1>
        <div class="dropdown">
            <button class="btn btn-secondary dropbtn">Perfil</button>
            <div class="dropdown-content">
                <a href="assets/pages/perfil.php">Perfil</a>
                <a href="assets/pages/admin_panel.html">Adm Usuario</a>
                <a href="assets/pages/dashboard.html">Dashboard</a>
                <a href="php/logout.php" class="text-danger">Salir</a>
            </div>
        </div>
    </div>

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

    <!-- Formulario de ingreso de transacciones -->
    <form id="finance-form" class="form-inline mb-4">
        <div class="form-group mr-2">
            <label for="type" class="mr-2">Tipo:</label>
            <select id="type" name="type" class="form-control" required>
                <option value="">Seleccionar</option>
                <option value="Ingreso">Ingreso</option>
                <option value="Egreso">Egreso</option>
            </select>
        </div>
        <div class="form-group mr-2">
            <label for="category" class="mr-2">Categoría:</label>
            <select id="category" name="category" class="form-control" required>
                <option value="">Seleccionar</option>
                <!-- Opciones serán cargadas dinámicamente -->
            </select>
        </div>
        <div id="subcategory-field" class="form-group mr-2" style="display: none;">
            <label for="subcategory" class="mr-2">Subcategoría:</label>
            <select id="subcategory" name="subcategory" class="form-control"></select>
        </div>
        <div id="patient-field" class="form-group mr-2" style="display: none;">
            <label for="patient" class="mr-2">Paciente:</label>
            <select id="patient" name="patient" class="form-control"></select>
        </div>
        <div class="form-group mr-2">
            <label for="amount" class="mr-2">Monto:</label>
            <input type="number" id="amount" name="amount" step="0.01" required>
        </div>
        <div class="form-group mr-2">
            <label for="comments" class="mr-2">Comentarios:</label>
            <input type="text" id="comments" name="comments" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Agregar</button>
    </form>

    <!-- Formulario de filtrado -->
    <h2 class="text-center my-4">Filtrar Transacciones</h2>   <form id="filter-form" class="form-inline mb-4">
        <div class="form-group mr-2">
            <label for="filter-month" class="mr-2">Mes:</label>
            <input type="month" id="filter-month" name="filter-month" class="form-control">
        </div>
        <div class="form-group mr-2">
            <label for="filter-type" class="mr-2">Tipo:</label>
            <select id="filter-type" name="filter-type" class="form-control">
                <option value="">Todos</option>
                <option value="Ingreso">Ingreso</option>
                <option value="Egreso">Egreso</option>
            </select>
        </div>
        <div class="form-group mr-2">
            <label for="filter-category" class="mr-2">Categoría:</label>
            <select id="filter-category" name="filter-category" class="form-control">
                <option value="">Todas</option>
                <!-- Opciones serán cargadas dinámicamente -->
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Filtrar</button>
    </form>

<!-- Información de balances -->
<div class="balance-info mb-4">
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
        <p id="total-savings-usd">--</p> <!-- Asegúrate de que este elemento esté presente -->
    </div>
</div>

    <!-- Tabla de transacciones -->
    <h2 class="text-center my-4">Transacciones</h2>
    <table id="transactions-table" class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Categoría</th>
                <th>Subcategoría</th>
                <th>Monto (UYU)</th>
                <th>Comentarios</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>
            <!-- Las filas de transacciones se agregarán aquí -->
        </tbody>
    </table>

    <!-- Paginación -->
    <div id="pagination-container" class="mb-4"></div>

    <!-- Gráficos -->
    <h2 class="text-center my-4">Gráficos</h2>
    <div class="chart-container">
        <canvas id="income-expense-chart" width="400" height="400"></canvas>
        <canvas id="expense-category-chart" width="400" height="400"></canvas>
        <canvas id="income-category-chart" width="400" height="400"></canvas>
        <canvas id="subcategory-gastos-fijos-chart" width="400" height="400"></canvas>
        <canvas id="subcategory-comida-chart" width="400" height="400"></canvas>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="/assets/js/scripts.js"></script>
</body>
</html>