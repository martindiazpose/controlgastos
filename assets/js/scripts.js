document.addEventListener('DOMContentLoaded', function () {
    const financeForm = document.getElementById('finance-form');
    const filterForm = document.getElementById('filter-form');
    const categorySelect = document.getElementById('category');
    const typeSelect = document.getElementById('type');
    const patientField = document.getElementById('patient-field');
    const patientSelect = document.getElementById('patient');
    const subcategoryField = document.getElementById('subcategory-field');
    const subcategorySelect = document.getElementById('subcategory');
    const filterCategorySelect = document.getElementById('filter-category');
    const filterMonthElement = document.getElementById('filter-month');
    const filterMonth = filterMonthElement && filterMonthElement.value ? filterMonthElement.value : new Date().toISOString().slice(0, 7); // Mes actual por defecto
    const filterCategoryElement = document.getElementById('filter-category');
    const filterCategory = filterCategoryElement && filterCategoryElement.value ? filterCategoryElement.value : ''; // Valor predeterminado: vacío
    const filterTypeElement = document.getElementById('filter-type');
    const filterType = filterTypeElement && filterTypeElement.value ? filterTypeElement.value : ''; // Valor predeterminado: vacío
    const paginationContainer = document.getElementById('pagination-container');
    const transactionsTableBody = document.querySelector('#transactions-table tbody');
    const receiptField = document.getElementById('receipt-field'); // Campo de comprobante de pago

    // Detectar la página actual
    const currentPage = window.location.pathname;

    // Verificar si estamos en dashboard.html
    const isDashboardPage = currentPage.includes('dashboard.html');

    // Verificar si estamos en index.php
    const isIndexPage = currentPage.includes('index.php');

    // Asegúrate de que los elementos existen antes de agregar event listeners
    if (financeForm) {
        financeForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(financeForm);
            fetch('./php/add_transaction.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showModal('Éxito', 'Transacción agregada correctamente.');
                        setTimeout(() => window.location.reload(), 3000);
                    } else {
                        showModal('Error', data.error || 'Error desconocido');
                    }
                })
                .catch(error => showModal('Error', 'Error: ' + error.message));
        });
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            fetchTransactions();
        });
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', function () {
            const type = typeSelect.value.toLowerCase();
            loadCategoriesByType(type);
            if (type === 'egreso') {
                receiptField.style.display = 'block'; // Muestra el campo de comprobante de pago
            } else {
                receiptField.style.display = 'none'; // Oculta el campo de comprobante de pago
            }
        });
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', function () {
            const categoryId = categorySelect.value;
            if (categoryId) {
                loadSubcategories(categoryId);
                const category = categorySelect.selectedOptions[0]?.textContent || '';
                if (category.includes('Paciente')) {
                    const type = category.includes('Mensual') ? 'mensual' : 'semanal';
                    patientField.style.display = 'block';
                    loadPatients(type);
                } else {
                    patientField.style.display = 'none';
                    patientSelect.innerHTML = '';
                }
            } else {
                subcategoryField.style.display = 'none';
                subcategorySelect.innerHTML = '';
            }
        });
    }

    // Load categories by type
    async function loadCategoriesByType(type) {
        try {
            const response = await fetch(`../php/get_categories_and_types.php?type=${type}`);
            const data = await response.json();
    
            if (data.status === 'success' && Array.isArray(data.categories)) {
                categorySelect.innerHTML = '<option value="">Seleccionar</option>';
                data.categories.forEach(category => {
                    const option = document.createElement("option");
                    option.value = category.id;
                    option.textContent = category.nombre;
                    categorySelect.appendChild(option);
                });
            } else {
                console.error('Error al cargar categorías:', data.error);
                categorySelect.innerHTML = '<option value="">Error al cargar categorías</option>';
            }
        } catch (error) {
            console.error('Error al cargar categorías:', error);
            categorySelect.innerHTML = '<option value="">Error al cargar categorías</option>';
        }
    }

    // Load subcategories by category ID
    async function loadSubcategories(categoryId) {
        try {
            const response = await fetch(`../php/get_subcategories.php?category_id=${categoryId}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.status === 'success' && Array.isArray(data.subcategories) && data.subcategories.length > 0) {
                subcategoryField.style.display = 'block';
                subcategorySelect.innerHTML = '<option value="">Seleccionar</option>';
                data.subcategories.forEach(subcategory => {
                    const option = document.createElement("option");
                    option.value = subcategory.id;
                    option.textContent = subcategory.nombre;
                    subcategorySelect.appendChild(option);
                });
            } else {
                subcategoryField.style.display = 'none';
                subcategorySelect.innerHTML = "";
            }
        } catch (error) {
            console.error('Error al cargar subcategorías:', error);
            subcategoryField.style.display = 'none';
            subcategorySelect.innerHTML = "";
            showModal('Error', 'Error al cargar subcategorías. Por favor, inténtelo de nuevo más tarde.');
        }
    }

    // Load all categories and types for the initial load
    async function loadCategoriesAndTypes() {
        try {
            const response = await fetch('../php/get_categories_and_types.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.status === 'success' && Array.isArray(data.categories)) {
                typeSelect.innerHTML = '<option value="">Seleccionar</option>';
                categorySelect.innerHTML = '<option value="">Seleccionar</option>';
                typeSelect.innerHTML += '<option value="Ingreso">Ingreso</option><option value="Egreso">Egreso</option>';
                data.categories.forEach(category => {
                    const option = document.createElement("option");
                    option.value = category.id;
                    option.textContent = category.nombre;
                    categorySelect.appendChild(option);
                });

                if (filterCategorySelect) {
                    filterCategorySelect.innerHTML = '<option value="">Todas</option>';
                    data.categories.forEach(category => {
                        const option = document.createElement("option");
                        option.value = category.id;
                        option.textContent = category.nombre;
                        filterCategorySelect.appendChild(option);
                    });
                }
            } else {
                console.error('Error al cargar categorías y tipos:', data.error);
                categorySelect.innerHTML = '<option value="">Error al cargar categorías y tipos</option>';
            }
        } catch (error) {
            console.error('Error al cargar categorías y tipos:', error);
            categorySelect.innerHTML = '<option value="">Error al cargar categorías y tipos</option>';
            showModal('Error', 'Error al cargar categorías y tipos. Por favor, inténtelo de nuevo más tarde.');
        }
    }

    // Load patients based on the type (mensual o semanal)
    async function loadPatients(type) {
        try {
            const response = await fetch(`./php/obtener_pacientes.php?type=${type}`);
            const data = await response.json();
    
            if (data.status === 'success' && Array.isArray(data.patients)) {
                patientSelect.innerHTML = '<option value="">Seleccionar</option>';
                data.patients.forEach(patient => {
                    const option = document.createElement("option");
                    option.value = patient.nombre;
                    option.textContent = patient.nombre;
                    patientSelect.appendChild(option);
                });
            } else {
                console.error('Error al cargar pacientes:', data.error);
                patientSelect.innerHTML = '<option value="">Error al cargar pacientes</option>';
            }
        } catch (error) {
            console.error('Error al cargar pacientes:', error);
            patientSelect.innerHTML = '<option value="">Error al cargar pacientes</option>';
        }
    }

    // Show modal with message
    function showModal(title, message) {
        const modalElement = document.getElementById('messageModal');
        if (modalElement) {
            const messageModal = new bootstrap.Modal(modalElement);
            document.getElementById('messageModalLabel').innerText = title;
            document.getElementById('modalMessage').innerText = message;
            messageModal.show();
        } else {
            console.error('El modal no existe en el DOM.');
        }
    }

    // Variables para almacenar las instancias de los gráficos
    let incomeExpenseChart, expenseCategoryChart, incomeCategoryChart, gastosFijosSubcategoryChart, categoryComidaChart;

    // Variable global para almacenar las transacciones
    let transactions = [];

    // Fetch transactions based on filters
    async function fetchTransactions(page = 1) {
        const params = new URLSearchParams({
            month: filterMonth,
            type: filterType,
            category: filterCategory,
            page: page
        });

        try {
            const response = await fetch(`../../php/get_transactions.php?${params.toString()}`);
            const data = await response.json();

            if (data.status === 'success') {
                transactions = data.transactions; // Actualizar el array global
                renderTable(data.transactions); // Renderiza la tabla
                updateBalance(data); // Actualiza el balance
                renderCharts(data.transactions); // Renderiza las gráficas

                // Llama a initializeDashboardPage después de renderizar la tabla
                initializeDashboardPage();
            } else {
                console.error('Error al cargar transacciones:', data.error);
            }
        } catch (error) {
            console.error('Error al cargar transacciones:', error);
        }
    }

    // Renderiza la tabla de transacciones
    function renderTable(data) {
        const tbody = document.getElementById('transactions-table')?.querySelector('tbody');
        if (!tbody) {
            console.warn('No se encontró el elemento <tbody> en la tabla.');
            return;
        }
    
        tbody.innerHTML = ''; // Limpia el contenido anterior
    
        if (data.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td colspan="9" class="text-center">No hay transacciones disponibles.</td>`;
            tbody.appendChild(tr);
            return;
        }
    
        data.forEach(transaction => {
            console.log('Renderizando fila:', transaction);
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${transaction.fecha || ''}</td>
                <td>${transaction.tipo || ''}</td>
                <td>${transaction.categoria_nombre || ''}</td>
                <td>${transaction.subcategoria || ''}</td>
                <td>${parseFloat(transaction.monto).toLocaleString('es-UY', { style: 'currency', currency: 'UYU' })}</td>
                <td>${transaction.comentarios || ''}</td>
                <td>${transaction.paciente || ''}</td>
                <td>
                    <button class="btn btn-info btn-sm view-transaction-btn" data-id="${transaction.id}">Ver</button>
                    <button class="btn btn-danger btn-sm delete-transaction-btn" data-id="${transaction.id}">Eliminar</button>
                </td>
                <td>
                    ${
                        transaction.comprobante && transaction.comprobante.trim() !== '' && transaction.comprobante !== 'null'
                            ? `<a href="${transaction.comprobante}" target="_blank" class="btn btn-primary btn-sm">Ver Comprobante</a>`
                            : ''
                    }
                </td>
            `;
            tbody.appendChild(tr);
        });
    
        // Inicializa los eventos de los botones después de renderizar la tabla
        initializeDashboardPage();
    }
    
    // Muestra los detalles de una transacción en un modal
    function showTransactionDetails(transaction) {
        if (!transaction) {
            console.error('Transacción no encontrada.');
            return;
        }
    
        // Actualiza el contenido del modal con los detalles de la transacción
        document.getElementById('modal-transaction-id').innerText = transaction.id || '';
        document.getElementById('modal-transaction-date').innerText = transaction.fecha || '';
        document.getElementById('modal-transaction-type').innerText = transaction.tipo || '';
        document.getElementById('modal-transaction-category').innerText = transaction.categoria_nombre || '';
        document.getElementById('modal-transaction-amount').innerText = parseFloat(transaction.monto).toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
        document.getElementById('modal-transaction-comments').innerText = transaction.comentarios || '';
    
        // Muestra el modal
        const transactionModal = new bootstrap.Modal(document.getElementById('transactionModal'));
        transactionModal.show();
    }
    
    // Inicializa los eventos de los botones "Ver" y "Eliminar"
    function initializeDashboardPage() {
        const viewButtons = document.querySelectorAll('.view-transaction-btn');
        const deleteButtons = document.querySelectorAll('.delete-transaction-btn');
    
        console.log('Botones "Ver":', viewButtons);
        console.log('Botones "Eliminar":', deleteButtons);
    
        // Elimina eventos previos para evitar duplicados
        viewButtons.forEach(button => {
            button.removeEventListener('click', handleViewButtonClick);
            button.addEventListener('click', handleViewButtonClick);
        });
    
        deleteButtons.forEach(button => {
            button.removeEventListener('click', handleDeleteButtonClick);
            button.addEventListener('click', handleDeleteButtonClick);
        });
    }
    
    // Maneja el evento del botón "Ver"
    function handleViewButtonClick() {
        const transactionId = this.getAttribute('data-id');
        const transaction = getTransactionById(transactionId);
        if (transaction) {
            showTransactionDetails(transaction);
        } else {
            console.error(`Transacción con ID ${transactionId} no encontrada.`);
        }
    }
    
    // Maneja el evento del botón "Eliminar"
    function handleDeleteButtonClick() {
        const transactionId = this.getAttribute('data-id');
        showDeleteConfirmationModal(transactionId);
    }
    
    // Encuentra una transacción por su ID
    function getTransactionById(id) {
        return transactions.find(transaction => transaction.id == id);
    }
    
    // Muestra el modal de confirmación para eliminar una transacción
    function showDeleteConfirmationModal(id) {
        transactionToDelete = id; // Almacena el ID de la transacción a eliminar
    
        // Muestra el modal de confirmación
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        confirmDeleteModal.show();
    }
    
    // Elimina una transacción
    function deleteTransaction(id) {
        console.log(`Eliminar transacción con ID: ${id}`);
    
        fetch('../../php/delete_transaction.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id }), // Envía el ID de la transacción al backend
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                console.log(`Transacción con ID ${id} eliminada correctamente.`);
                showModal('Éxito', 'La transacción ha sido eliminada correctamente.');
    
                // Cierra el modal de confirmación
                const confirmDeleteModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
                if (confirmDeleteModal) {
                    confirmDeleteModal.hide();
                }
    
                fetchTransactions(); // Recarga las transacciones después de eliminar
            } else {
                console.error(`Error al eliminar la transacción con ID ${id}:`, data.error);
                showModal('Error', data.error || 'No se pudo eliminar la transacción.');
            }
        })
        .catch(error => {
            console.error(`Error al eliminar la transacción con ID ${id}:`, error);
            showModal('Error', 'Ocurrió un error al intentar eliminar la transacción.');
        });
    }
    
    // Actualizar balance
    async function updateBalance(data) {
        if (!data || !data.transactions) {
            console.warn('No hay datos para actualizar el balance.');
            return;
        }
    
        console.log('Datos para actualizar el balance:', data); // Depuración
    
        // Calcula los totales a partir de las transacciones
        const transactions = data.transactions;
    
        const totalIncome = transactions
            .filter(t => t.tipo === 'Ingreso')
            .reduce((sum, t) => sum + parseFloat(t.monto), 0);
    
        const totalExpense = transactions
            .filter(t => t.tipo === 'Egreso')
            .reduce((sum, t) => sum + parseFloat(t.monto), 0);
    
        const totalSavings = transactions
            .filter(t => t.categoria_nombre === 'Ahorros')
            .reduce((sum, t) => sum + parseFloat(t.monto), 0);
    
        // Calcula el balance general
        const totalBalance = totalIncome - totalExpense;
    
        // Obtén el tipo de cambio dinámico
        const exchangeRateUYUtoUSD = await getExchangeRate();
        const totalSavingsUSD = exchangeRateUYUtoUSD ? totalSavings * exchangeRateUYUtoUSD : 0;
    
        // Actualiza los elementos del DOM
        const totalIncomeElement = document.getElementById('total-income');
        if (totalIncomeElement) {
            totalIncomeElement.innerText = totalIncome.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
        }
    
        const totalExpenseElement = document.getElementById('total-expense');
        if (totalExpenseElement) {
            totalExpenseElement.innerText = totalExpense.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
        }
    
        const totalBalanceElement = document.getElementById('total-balance');
        if (totalBalanceElement) {
            totalBalanceElement.innerText = totalBalance.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
        }
    
        const totalSavingsElement = document.getElementById('total-savings');
        if (totalSavingsElement) {
            totalSavingsElement.innerText = totalSavings.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
        }
    
        const totalSavingsUSDElement = document.getElementById('total-savings-usd');
        if (totalSavingsUSDElement) {
            if (exchangeRateUYUtoUSD) {
                totalSavingsUSDElement.innerText = totalSavingsUSD.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
            } else {
                totalSavingsUSDElement.innerText = 'No disponible'; // Si no se pudo obtener el tipo de cambio
            }
        }
    }

    function renderCharts(transactions) {
        if (!transactions || transactions.length === 0) {
            console.warn('No hay transacciones para renderizar las gráficas.');
            return;
        }
    
        console.log('Transacciones para las gráficas:', transactions);
    
        // Gráfica de Ingresos vs Egresos
        const incomeExpenseCanvas = document.getElementById('income-expense-chart');
        if (incomeExpenseCanvas) {
            const totalIncome = transactions
                .filter(t => t.tipo === 'Ingreso')
                .reduce((sum, t) => sum + parseFloat(t.monto), 0);
    
            const totalExpense = transactions
                .filter(t => t.tipo === 'Egreso')
                .reduce((sum, t) => sum + parseFloat(t.monto), 0);
    
            if (incomeExpenseChart) {
                incomeExpenseChart.destroy();
            }
    
            incomeExpenseChart = new Chart(incomeExpenseCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Ingresos', 'Egresos'],
                    datasets: [{
                        data: [totalIncome, totalExpense],
                        backgroundColor: ['#36A2EB', '#FF6384'],
                        hoverBackgroundColor: ['#36A2EB80', '#FF638480'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Ingresos vs Egresos'
                        }
                    }
                }
            });
        }
    
        // Gráfica de Categorías de Egresos
        const expenseCategoryCanvas = document.getElementById('expense-category-chart');
        if (expenseCategoryCanvas) {
            const expenseCategories = transactions
                .filter(t => t.tipo === 'Egreso')
                .reduce((categories, t) => {
                    categories[t.categoria_nombre] = (categories[t.categoria_nombre] || 0) + parseFloat(t.monto);
                    return categories;
                }, {});
    
            const labels = Object.keys(expenseCategories);
            const data = Object.values(expenseCategories);
    
            if (expenseCategoryChart) {
                expenseCategoryChart.destroy();
            }
    
            expenseCategoryChart = new Chart(expenseCategoryCanvas, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                        hoverBackgroundColor: ['#FF638480', '#36A2EB80', '#FFCE5680', '#4BC0C080', '#9966FF80'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Categorías de Egresos'
                        }
                    }
                }
            });
        }
    
        // Gráfica de Categorías de Ingresos
        const incomeCategoryCanvas = document.getElementById('income-category-chart');
        if (incomeCategoryCanvas) {
            const incomeCategories = transactions
                .filter(t => t.tipo === 'Ingreso')
                .reduce((categories, t) => {
                    categories[t.categoria_nombre] = (categories[t.categoria_nombre] || 0) + parseFloat(t.monto);
                    return categories;
                }, {});
    
            const labels = Object.keys(incomeCategories);
            const data = Object.values(incomeCategories);
    
            if (incomeCategoryChart) {
                incomeCategoryChart.destroy();
            }
    
            incomeCategoryChart = new Chart(incomeCategoryCanvas, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF'],
                        hoverBackgroundColor: ['#36A2EB80', '#FF638480', '#FFCE5680', '#4BC0C080', '#9966FF80'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Categorías de Ingresos'
                        }
                    }
                }
            });
        }
    
        // Gráfica de Subcategorías de Gastos Fijos
        const gastosFijosCanvas = document.getElementById('subcategory-gastos-fijos-chart');
        if (gastosFijosCanvas) {
            const gastosFijos = transactions
                .filter(t => t.categoria_nombre === 'Gastos Fijos')
                .reduce((subcategories, t) => {
                    subcategories[t.subcategoria] = (subcategories[t.subcategoria] || 0) + parseFloat(t.monto);
                    return subcategories;
                }, {});
    
            const labels = Object.keys(gastosFijos);
            const data = Object.values(gastosFijos);
    
            if (gastosFijosSubcategoryChart) {
                gastosFijosSubcategoryChart.destroy();
            }
    
            gastosFijosSubcategoryChart = new Chart(gastosFijosCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: '#FF6384',
                        hoverBackgroundColor: '#FF638480',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        title: {
                            display: true,
                            text: 'Subcategorías de Gastos Fijos'
                        }
                    }
                }
            });
        }
    
        // Gráfica de Categoría Comida
        const comidaCanvas = document.getElementById('category-comida-chart');
        if (comidaCanvas) {
            const comidaSubcategories = transactions
                .filter(t => t.categoria_nombre === 'Comida')
                .reduce((subcategories, t) => {
                    subcategories[t.subcategoria] = (subcategories[t.subcategoria] || 0) + parseFloat(t.monto);
                    return subcategories;
                }, {});
    
            const labels = Object.keys(comidaSubcategories);
            const data = Object.values(comidaSubcategories);
    
            if (categoryComidaChart) {
                categoryComidaChart.destroy();
            }
    
            categoryComidaChart = new Chart(comidaCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: '#36A2EB',
                        hoverBackgroundColor: '#36A2EB80',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        title: {
                            display: true,
                            text: 'Subcategorías de Comida'
                        }
                    }
                }
            });
        }
    }

    // Encuentra una transacción por su ID
    function getTransactionById(id) {
        return transactions.find(transaction => transaction.id == id);
    }

    // Muestra el modal de confirmación para eliminar una transacción
    function showDeleteConfirmationModal(id) {
        transactionToDelete = id; // Almacena el ID de la transacción a eliminar
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        confirmDeleteModal.show();
    }

    let transactionToDelete = null; // Variable para almacenar el ID de la transacción a eliminar

    document.getElementById('confirmDeleteButton').addEventListener('click', function () {
        if (transactionToDelete) {
            deleteTransaction(transactionToDelete); // Llama a la función para eliminar la transacción
            transactionToDelete = null; // Resetea la variable
        }
    });

    function deleteTransaction(id) {
        console.log(`Eliminar transacción con ID: ${id}`);
        console.log('Buscando transacción con ID:', id);

        fetch('../../php/delete_transaction.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id }), // Envía el ID de la transacción al backend
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                console.log(`Transacción con ID ${id} eliminada correctamente.`);
                showModal('Éxito', 'La transacción ha sido eliminada correctamente.');

                // Cierra el modal de confirmación
                const confirmDeleteModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
                if (confirmDeleteModal) {
                    confirmDeleteModal.hide();
                }

                fetchTransactions(); // Recarga las transacciones después de eliminar
            } else {
                console.error(`Error al eliminar la transacción con ID ${id}:`, data.error);
                showModal('Error', data.error || 'No se pudo eliminar la transacción.');
            }
        })
        .catch(error => {
            console.error(`Error al eliminar la transacción con ID ${id}:`, error);
            showModal('Error', 'Ocurrió un error al intentar eliminar la transacción.');
        });
    }

    // Cargar datos iniciales
    fetchTransactions();

    if (isDashboardPage) {
        initializeDashboardPage();
    } else {
        initializeIndexPage();
    }
});

function initializeDashboardPage() {
    const viewButtons = document.querySelectorAll('.view-transaction-btn');
    const deleteButtons = document.querySelectorAll('.delete-transaction-btn');

    console.log('Botones "Ver":', viewButtons);
    console.log('Botones "Eliminar":', deleteButtons);

    if (viewButtons.length > 0) {
        viewButtons.forEach(button => {
            button.addEventListener('click', function () {
                const transactionId = this.getAttribute('data-id');
                const transaction = getTransactionById(transactionId);
                if (transaction) {
                    showTransactionDetails(transaction);
                } else {
                    console.error(`Transacción con ID ${transactionId} no encontrada.`);
                }
            });
        });
    } else {
        console.warn('No se encontraron botones "Ver" en el DOM.');
    }

    if (deleteButtons.length > 0) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const transactionId = this.getAttribute('data-id');
                showDeleteConfirmationModal(transactionId);
            });
        });
    } else {
        console.warn('No se encontraron botones "Eliminar" en el DOM.');
    }
}

function initializeIndexPage() {
    fetchTransactions(); // Carga las transacciones sin botones "Ver" y "Eliminar"
}

// Define la función fuera de cualquier bloque para que sea accesible globalmente
function showDeleteConfirmationModal(id) {
    transactionToDelete = id; // Almacena el ID de la transacción a eliminar
    const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    confirmDeleteModal.show();
}

// Reinicia los modales al cerrarlos (solo se registra una vez)
document.getElementById('transactionModal').addEventListener('hidden.bs.modal', function () {
    // Limpia el contenido del modal de detalles
    document.getElementById('modal-transaction-id').innerText = '';
    document.getElementById('modal-transaction-date').innerText = '';
    document.getElementById('modal-transaction-type').innerText = '';
    document.getElementById('modal-transaction-category').innerText = '';
    document.getElementById('modal-transaction-amount').innerText = '';
    document.getElementById('modal-transaction-comments').innerText = '';
});

document.getElementById('confirmDeleteModal').addEventListener('hidden.bs.modal', function () {
    // Reinicia la variable de transacción a eliminar
    transactionToDelete = null;
});

// Cierra todos los modales al presionar la cruz, aceptar o cancelar
document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
    button.addEventListener('click', function () {
        const modalElement = this.closest('.modal');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const transactions = []; // Aquí debes cargar las transacciones del mes actual desde tu backend o API.
    const rowsPerPage = 10;
    let currentPage = 1;

    // Función para renderizar la tabla con paginación
    function renderTable(page) {
        const tableBody = document.querySelector("#transactions-table tbody");
        tableBody.innerHTML = "";

        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedTransactions = transactions.slice(start, end);

        paginatedTransactions.forEach(transaction => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${transaction.fecha}</td>
                <td>${transaction.tipo}</td>
                <td>${transaction.categoria}</td>
                <td>${transaction.subcategoria}</td>
                <td>${transaction.monto}</td>
                <td>${transaction.comentarios}</td>
                <td>${transaction.paciente}</td>
                <td>
                    <button class="btn btn-primary btn-sm">Ver</button>
                    <button class="btn btn-danger btn-sm">Eliminar</button>
                </td>
                <td>${transaction.comprobante || "N/A"}</td>
            `;
            tableBody.appendChild(row);
        });

        renderPaginationControls();
    }

    // Función para renderizar los controles de paginación
    function renderPaginationControls() {
        const paginationControls = document.querySelector("#pagination-controls");
        paginationControls.innerHTML = "";

        const totalPages = Math.ceil(transactions.length / rowsPerPage);

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement("li");
            li.className = `page-item ${i === currentPage ? "active" : ""}`;
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener("click", (e) => {
                e.preventDefault();
                currentPage = i;
                renderTable(currentPage);
            });
            paginationControls.appendChild(li);
        }
    }

    // Simulación de datos (debes reemplazar esto con datos reales del backend)
    function loadTransactions() {
        // Aquí puedes hacer una llamada AJAX o fetch para obtener las transacciones del mes actual
        for (let i = 1; i <= 50; i++) {
            transactions.push({
                fecha: `2025-03-${i < 10 ? "0" + i : i}`,
                tipo: i % 2 === 0 ? "Ingreso" : "Egreso",
                categoria: "Categoría " + (i % 5 + 1),
                subcategoria: "Subcategoría " + (i % 3 + 1),
                monto: (Math.random() * 1000).toFixed(2),
                comentarios: "Comentario " + i,
                paciente: "Paciente " + i,
                comprobante: i % 2 === 0 ? "Sí" : "No"
            });
        }
        renderTable(currentPage);
    }

    loadTransactions();
});

// Función para obtener el tipo de cambio UYU a USD
async function getExchangeRate() {
    const API_URL = `https://api.exchangerate.host/latest?base=UYU&symbols=USD`; // Sin access_key
    try {
        const response = await fetch(API_URL);
        const data = await response.json();

        if (data && data.rates && data.rates.USD) {
            return data.rates.USD; // Devuelve el tipo de cambio UYU a USD
        } else {
            console.error('No se pudo obtener el tipo de cambio. Respuesta inesperada:', data);
            return null;
        }
    } catch (error) {
        console.error('Error al obtener el tipo de cambio:', error);
        return null;
    }
}