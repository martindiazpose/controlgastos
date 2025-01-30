<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="icon" href="../img/carpeta.png" type="image/x-icon">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            color: #343a40;
        }
        .section {
            margin: 20px 0;
        }
        .input-group, .filter-group {
            margin: 20px auto;
            max-width: 600px;
        }
        .list-group {
            max-width: 1200px;
            margin: 20px auto;
        }
        .list-group-item {
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .modal-content {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            color: #343a40;
        }
        .modal-header, .modal-footer {
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
            background-color: #dc3545;
            border: none;
            color: #fff;
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
        }
        .btn-close:hover {
            background-color: #c82333;
        }
        .btn-secondary, .btn-secondary:focus, .btn-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
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
    </style>
</head>
<body class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
    <h1>Administracion Usuario</h1>
        <div class="dropdown">
            <button class="btn btn-secondary dropbtn">Perfil</button>
            <div class="dropdown-content">
                <a href="assets/pages/perfil.php">Perfil</a>
                <a href="assets/pages/admin_panel.php">Adm Usuario</a>
                <a href="assets/pages/dashboard.html">Dashboard</a>
                <a href="php/logout.php" class="text-danger">Salir</a>
            </div>
        </div>
    </div>
    

    <!-- Pestañas de navegación -->
    <ul class="nav nav-tabs" id="adminTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="ingresos-tab" data-toggle="tab" href="#ingresos" role="tab" aria-controls="ingresos" aria-selected="true">Gestión de Ingresos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="egresos-tab" data-toggle="tab" href="#egresos" role="tab" aria-controls="egresos" aria-selected="false">Gestión de Egresos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pacientes-tab" data-toggle="tab" href="#pacientes" role="tab" aria-controls="pacientes" aria-selected="false">Gestión de Pacientes</a>
        </li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="adminTabContent">
        <!-- Gestión de Ingresos -->
        <div class="tab-pane fade show active" id="ingresos" role="tabpanel" aria-labelledby="ingresos-tab">
            <div class="section">
                <h2>Gestión de Categorías de Ingresos</h2>
                <div class="input-group mb-3">
                    <input type="text" id="income-category-name" class="form-control" placeholder="Nombre de la categoría">
                    <div class="input-group-append">
                        <button id="add-income-category" class="btn btn-primary">Agregar Categoría</button>
                    </div>
                </div>
                <div class="filter-group mb-3">
                    <input type="text" id="search-income-category" class="form-control" placeholder="Buscar categoría">
                </div>
                <div class="row" id="income-category-list"></div>

                <h2>Gestión de Subcategorías de Ingresos</h2>
                <div class="input-group mb-3">
                    <input type="text" id="income-subcategory-name" class="form-control" placeholder="Nombre de la subcategoría">
                    <select id="income-subcategory-category" class="form-control">
                        <option disabled selected>Seleccionar categoría...</option>
                    </select>
                    <div class="input-group-append">
                        <button id="add-income-subcategory" class="btn btn-primary">Agregar Subcategoría</button>
                    </div>
                </div>
                <div class="filter-group mb-3">
                    <input type="text" id="search-income-subcategory" class="form-control" placeholder="Buscar subcategoría">
                </div>
                <div class="row" id="income-subcategory-list"></div>
            </div>
        </div>

        <!-- Gestión de Egresos -->
        <div class="tab-pane fade" id="egresos" role="tabpanel" aria-labelledby="egresos-tab">
            <div class="section">
                <h2>Gestión de Categorías de Egresos</h2>
                <div class="input-group mb-3">
                    <input type="text" id="expense-category-name" class="form-control" placeholder="Nombre de la categoría">
                    <div class="input-group-append">
                        <button id="add-expense-category" class="btn btn-primary">Agregar Categoría</button>
                    </div>
                </div>
                <div class="filter-group mb-3">
                    <input type="text" id="search-expense-category" class="form-control" placeholder="Buscar categoría">
                </div>
                <div class="row" id="expense-category-list"></div>

                <h2>Gestión de Subcategorías de Egresos</h2>
                <div class="input-group mb-3">
                    <input type="text" id="expense-subcategory-name" class="form-control" placeholder="Nombre de la subcategoría">
                    <select id="expense-subcategory-category" class="form-control">
                        <option disabled selected>Seleccionar categoría...</option>
                    </select>
                    <div class="input-group-append">
                        <button id="add-expense-subcategory" class="btn btn-primary">Agregar Subcategoría</button>
                    </div>
                </div>
                <div class="filter-group mb-3">
                    <input type="text" id="search-expense-subcategory" class="form-control" placeholder="Buscar subcategoría">
                </div>
                <div class="row" id="expense-subcategory-list"></div>
            </div>
        </div>

        <!-- Gestión de Pacientes -->
        <div class="tab-pane fade" id="pacientes" role="tabpanel" aria-labelledby="pacientes-tab">
            <div class="section">
                <h2>Gestión de Pacientes</h2>
                <div class="input-group mb-3">
                    <input type="text" id="patient-name" class="form-control" placeholder="Nombre del paciente">
                    <select id="patient-type" class="form-control">
                        <option disabled selected>Seleccionar...</option>
                        <option value="mensual">Mensual</option>
                        <option value="semanal">Semanal</option>
                    </select>
                    <div class="input-group-append">
                        <button id="add-patient" class="btn btn-primary">Agregar Paciente</button>
                    </div>
                </div>
                <div class="filter-group mb-3">
                    <input type="text" id="search-patient" class="form-control" placeholder="Buscar paciente">
                </div>
                <div class="filter-group mb-3">
                    <select id="filter-type" class="form-control">
                        <option value="all">Todos</option>
                        <option value="mensual">Mensuales</option>
                        <option value="semanal">Semanales</option>
                    </select>
                </div>
                <div class="row" id="patient-list"></div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación y mensajes -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Mensaje</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalMessage">
                    <!-- Mensaje se mostrará aquí -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmación de Eliminación</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Mensaje de confirmación se mostrará aquí -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
<!-- Scripts -->
<script>
    $(document).ready(function() {
        // Cargar categorías y subcategorías
        loadIncomeCategories();
        loadExpenseCategories();
        loadIncomeSubcategories();
        loadExpenseSubcategories();
        loadPatients();

        // Añadir categoría de ingresos
        $('#add-income-category').click(function() {
            let name = $('#income-category-name').val().trim();
            if (name) {
                $.post('https://controlgastos.wantedstudio.uy/php/manage_categories.php', { action: 'add', name: name, type: 'income' }, function(response) {
                    if (response.status === 'success') {
                        $('#modalMessage').text(`Categoría ${name} agregada exitosamente.`);
                    } else {
                        $('#modalMessage').text(`Error al agregar categoría: ${response.error}`);
                    }
                    $('#messageModal').modal('show');
                    loadIncomeCategories();
                    $('#income-category-name').val('');
                }, 'json').fail(function() {
                    $('#modalMessage').text('Error al agregar categoría. Intente nuevamente más tarde.');
                    $('#messageModal').modal('show');
                });
            } else {
                $('#modalMessage').text('Por favor, complete todos los campos correctamente.');
                $('#messageModal').modal('show');
            }
        });

        // Añadir categoría de egresos
        $('#add-expense-category').click(function() {
            let name = $('#expense-category-name').val().trim();
            if (name) {
                $.post('https://controlgastos.wantedstudio.uy/php/manage_categories.php', { action: 'add', name: name, type: 'expense' }, function(response) {
                    if (response.status === 'success') {
                        $('#modalMessage').text(`Categoría ${name} agregada exitosamente.`);
                    } else {
                        $('#modalMessage').text(`Error al agregar categoría: ${response.error}`);
                    }
                    $('#messageModal').modal('show');
                    loadExpenseCategories();
                    $('#expense-category-name').val('');
                }, 'json').fail(function() {
                    $('#modalMessage').text('Error al agregar categoría. Intente nuevamente más tarde.');
                    $('#messageModal').modal('show');
                });
            } else {
                $('#modalMessage').text('Por favor, complete todos los campos correctamente.');
                $('#messageModal').modal('show');
            }
        });

        // Añadir subcategoría de ingresos
        $('#add-income-subcategory').click(function() {
            let name = $('#income-subcategory-name').val().trim();
            let categoryId = $('#income-subcategory-category').val();
            if (name && categoryId) {
                $.post('https://controlgastos.wantedstudio.uy/php/manage_subcategories.php', { action: 'add', name: name, category_id: categoryId }, function(response) {
                    if (response.status === 'success') {
                        $('#modalMessage').text(`Subcategoría ${name} agregada exitosamente.`);
                    } else {
                        $('#modalMessage').text(`Error al agregar subcategoría: ${response.error}`);
                    }
                    $('#messageModal').modal('show');
                    loadIncomeSubcategories();
                    $('#income-subcategory-name').val('');
                    $('#income-subcategory-category').val('');
                }, 'json').fail(function() {
                    $('#modalMessage').text('Error al agregar subcategoría. Intente nuevamente más tarde.');
                    $('#messageModal').modal('show');
                });
            } else {
                $('#modalMessage').text('Por favor, complete todos los campos correctamente.');
                $('#messageModal').modal('show');
            }
        });

        // Añadir subcategoría de egresos
        $('#add-expense-subcategory').click(function() {
            let name = $('#expense-subcategory-name').val().trim();
            let categoryId = $('#expense-subcategory-category').val();
            if (name && categoryId) {
                $.post('https://controlgastos.wantedstudio.uy/php/manage_subcategories.php', { action: 'add', name: name, category_id: categoryId }, function(response) {
                    if (response.status === 'success') {
                        $('#modalMessage').text(`Subcategoría ${name} agregada exitosamente.`);
                    } else {
                        $('#modalMessage').text(`Error al agregar subcategoría: ${response.error}`);
                    }
                    $('#messageModal').modal('show');
                    loadExpenseSubcategories();
                    $('#expense-subcategory-name').val('');
                    $('#expense-subcategory-category').val('');
                }, 'json').fail(function() {
                    $('#modalMessage').text('Error al agregar subcategoría. Intente nuevamente más tarde.');
                    $('#messageModal').modal('show');
                });
            } else {
                $('#modalMessage').text('Por favor, complete todos los campos correctamente.');
                $('#messageModal').modal('show');
            }
        });

        // Eliminar categoría
        $(document).on('click', '.delete-category', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            $.post('https://controlgastos.wantedstudio.uy/php/manage_categories.php', { action: 'delete', id: id }, function(response) {
                if (response.status === 'success') {
                    $('#modalMessage').text(`Categoría ${name} eliminada exitosamente.`);
                } else {
                    $('#modalMessage').text(`Error al eliminar categoría: ${response.error}`);
                }
                $('#messageModal').modal('show');
                loadIncomeCategories();
                loadExpenseCategories();
            }, 'json').fail(function() {
                $('#modalMessage').text('Error al eliminar categoría. Intente nuevamente más tarde.');
                $('#messageModal').modal('show');
            });
        });

        // Eliminar subcategoría
        $(document).on('click', '.delete-subcategory', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            $.post('https://controlgastos.wantedstudio.uy/php/manage_subcategories.php', { action: 'delete', id: id }, function(response) {
                if (response.status === 'success') {
                    $('#modalMessage').text(`Subcategoría ${name} eliminada exitosamente.`);
                } else {
                    $('#modalMessage').text(`Error al eliminar subcategoría: ${response.error}`);
                }
                $('#messageModal').modal('show');
                loadIncomeSubcategories();
                loadExpenseSubcategories();
            }, 'json').fail(function() {
                $('#modalMessage').text('Error al eliminar subcategoría. Intente nuevamente más tarde.');
                $('#messageModal').modal('show');
            });
        });

        // Cargar categorías de ingresos
        function loadIncomeCategories() {
            $.get('https://controlgastos.wantedstudio.uy/php/manage_categories.php', { action: 'list', type: 'income' }, function(response) {
                let categoryList = $('#income-category-list');
                let subcategoryCategorySelect = $('#income-subcategory-category');
                categoryList.empty();
                subcategoryCategorySelect.empty();
                subcategoryCategorySelect.append('<option disabled selected>Seleccionar categoría...</option>');
                if (Array.isArray(response)) {
                    response.forEach(function(category) {
                        categoryList.append(`<div class="col-md-4"><div class="list-group-item d-flex justify-content-between align-items-center">
                            ${category.nombre}
                            <button class="btn btn-danger btn-sm delete-category" data-id="${category.id}" data-name="${category.nombre}">Eliminar</button>
                        </div></div>`);
                        subcategoryCategorySelect.append(`<option value="${category.id}">${category.nombre}</option>`);
                    });
                } else {
                    $('#modalMessage').text(`Error al cargar lista de categorías: ${response.error}`);
                    $('#messageModal').modal('show');
                }
            }, 'json').fail(function() {
                $('#modalMessage').text('Error al cargar lista de categorías. Intente nuevamente más tarde.');
                $('#messageModal').modal('show');
            });
        }

        // Cargar categorías de egresos
        function loadExpenseCategories() {
            $.get('https://controlgastos.wantedstudio.uy/php/manage_categories.php', { action: 'list', type: 'expense' }, function(response) {
                let categoryList = $('#expense-category-list');
                let subcategoryCategorySelect = $('#expense-subcategory-category');
                categoryList.empty();
                subcategoryCategorySelect.empty();
                subcategoryCategorySelect.append('<option disabled selected>Seleccionar categoría...</option>');
                if (Array.isArray(response)) {
                    response.forEach(function(category) {
                        categoryList.append(`<div class="col-md-4"><div class="list-group-item d-flex justify-content-between align-items-center">
                            ${category.nombre}
                            <button class="btn btn-danger btn-sm delete-category" data-id="${category.id}" data-name="${category.nombre}">Eliminar</button>
                        </div></div>`);
                        subcategoryCategorySelect.append(`<option value="${category.id}">${category.nombre}</option>`);
                    });
                } else {
                    $('#modalMessage').text(`Error al cargar lista de categorías: ${response.error}`);
                    $('#messageModal').modal('show');
                }
            }, 'json').fail(function() {
                $('#modalMessage').text('Error al cargar lista de categorías. Intente nuevamente más tarde.');
                $('#messageModal').modal('show');
            });
        }

        // Cargar subcategorías de ingresos
        function loadIncomeSubcategories() {
            $.get('https://controlgastos.wantedstudio.uy/php/manage_subcategories.php', { action: 'list', type: 'income' }, function(response) {
                let subcategoryList = $('#income-subcategory-list');
                subcategoryList.empty();
                if (Array.isArray(response)) {
                    response.forEach(function(subcategory) {
                        subcategoryList.append(`<div class="col-md-4"><div class="list-group-item d-flex justify-content-between align-items-center">
                            ${subcategory.nombre} (Categoría: ${subcategory.categoria_nombre})
                            <button class="btn btn-danger btn-sm delete-subcategory" data-id="${subcategory.id}" data-name="${subcategory.nombre}">Eliminar</button>
                        </div></div>`);
                    });
                } else {
                    $('#modalMessage').text(`Error al cargar lista de subcategorías: ${response.error}`);
                    $('#messageModal').modal('show');
                }
            }, 'json').fail(function() {
                $('#modalMessage').text('Error al cargar lista de subcategorías. Intente nuevamente más tarde.');
                $('#messageModal').modal('show');
            });
        }

        // Cargar subcategorías de egresos
        function loadExpenseSubcategories() {
            $.get('https://controlgastos.wantedstudio.uy/php/manage_subcategories.php', { action: 'list', type: 'expense' }, function(response) {
                let subcategoryList = $('#expense-subcategory-list');
                subcategoryList.empty();
                if (Array.isArray(response)) {
                    response.forEach(function(subcategory) {
                        subcategoryList.append(`<div class="col-md-4"><div class="list-group-item d-flex justify-content-between align-items-center">
                            ${subcategory.nombre} (Categoría: ${subcategory.categoria_nombre})
                            <button class="btn btn-danger btn-sm delete-subcategory" data-id="${subcategory.id}" data-name="${subcategory.nombre}">Eliminar</button>
                        </div></div>`);
                    });
                } else {
                    $('#modalMessage').text(`Error al cargar lista de subcategorías: ${response.error}`);
                    $('#messageModal').modal('show');
                }
            }, 'json').fail(function() {
                $('#modalMessage').text('Error al cargar lista de subcategorías. Intente nuevamente más tarde.');
                $('#messageModal').modal('show');
            });
        }

        // Cargar pacientes
        function loadPatients() {
            let filterType = $('#filter-type').val();
            $.get('https://controlgastos.wantedstudio.uy/php/manage_patients.php', { action: 'list', type: filterType }, function(response) {
                let patientList = $('#patient-list');
                patientList.empty();
                if (Array.isArray(response)) {
                    response.forEach(function(patient) {
                        patientList.append(`<div class="col-md-4"><div class="list-group-item d-flex justify-content-between align-items-center">
                            ${patient.nombre} (${patient.tipo})
                            <button class="btn btn-danger btn-sm delete-patient" data-id="${patient.id}" data-name="${patient.nombre}" data-type="${patient.tipo}">Eliminar</button>
                        </div></div>`);
                    });
                } else {
                    $('#modalMessage').text(`Error al cargar lista de pacientes: ${response.error}`);
                    $('#messageModal').modal('show');
                }
            }, 'json').fail(function() {
                $('#modalMessage').text('Error al cargar lista de pacientes. Intente nuevamente más tarde.');
                $('#messageModal').modal('show');
            });
        }

        $('#filter-type').change(function() {
            loadPatients();
        });

        // Confirmar eliminación de paciente
        $(document).on('click', '.delete-patient', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let type = $(this).data('type');
            $('#confirmDeleteModal .modal-body').text(`¿Está seguro de que desea eliminar al paciente ${name} de la lista de ${type}?`);
            $('#confirmDeleteButton').data('id', id);
            $('#confirmDeleteButton').data('name', name);
            $('#confirmDeleteButton').data('type', type);
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteButton').click(function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let type = $(this).data('type');
            $.post('https://controlgastos.wantedstudio.uy/php/manage_patients.php', { action: 'delete', id: id }, function(response) {
                if (response.status === 'success') {
                    $('#modalMessage').text(`Paciente ${name} eliminado exitosamente de la lista de ${type}.`);
                } else {
                    $('#modalMessage').text(`Error al eliminar paciente: ${response.error}`);
                }
                $('#messageModal').modal('show');
                loadPatients();
            }, 'json').fail(function() {
                $('#modalMessage').text('Error al eliminar paciente. Intente nuevamente más tarde.');
                $('#messageModal').modal('show');
            });
            $('#confirmDeleteModal').modal('hide');
        });

        // Búsqueda en categorías de ingresos
        $('#search-income-category').on('input', function() {
            let search = $(this).val().toLowerCase();
            $('#income-category-list .list-group-item').each(function() {
                let item = $(this).text().toLowerCase();
                $(this).toggle(item.indexOf(search) !== -1);
            });
        });

        // Búsqueda en categorías de egresos
        $('#search-expense-category').on('input', function() {
            let search = $(this).val().toLowerCase();
            $('#expense-category-list .list-group-item').each(function() {
                let item = $(this).text().toLowerCase();
                $(this).toggle(item.indexOf(search) !== -1);
            });
        });

        // Búsqueda en lista de pacientes
        $('#search-patient').on('input', function() {
            let search = $(this).val().toLowerCase();
            $('#patient-list .list-group-item').each(function() {
                let item = $(this).text().toLowerCase();
                $(this).toggle(item.indexOf(search) !== -1);
            });
        });
    });
</script>
</body>
</html>