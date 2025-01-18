document.addEventListener('DOMContentLoaded', function () {
    const financeForm = document.getElementById('finance-form');
    const filterForm = document.getElementById('filter-form');
    const categorySelect = document.getElementById('category');
    const typeSelect = document.getElementById('type');
    const patientField = document.getElementById('patient-field');
    const patientSelect = document.getElementById('patient');

    // Event listener for finance form submission
    if (financeForm) {
        financeForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(financeForm);

            fetch('./php/add_transaction.php', {
                method: 'POST',
                body: formData
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

    // Event listener for filter form submission
    if (filterForm) {
        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            fetchTransactions();
        });
    }

    // Load categories based on type selection
    typeSelect.addEventListener('change', function () {
        const type = typeSelect.value.toLowerCase();
        loadCategoriesByType(type);
    });

    // Load patients based on category selection
    categorySelect.addEventListener('change', function () {
        const category = categorySelect.selectedOptions[0]?.textContent || '';
        if (category.includes('Paciente')) {
            const type = category.includes('Mensual') ? 'mensual' : 'semanal';
            patientField.style.display = 'block';
            loadPatients(type);
        } else {
            patientField.style.display = 'none';
            patientSelect.innerHTML = "";
        }
    });

    // Load categories by type
    async function loadCategoriesByType(type) {
        try {
            const response = await fetch(`./php/get_categories_and_types.php?type=${type}`);
            const data = await response.json();

            if (data.status === 'success' && Array.isArray(data.categories)) {
                categorySelect.innerHTML = '<option value="">Seleccionar</option>';
                data.categories.forEach(category => {
                    const option = document.createElement("option");
                    option.value = category.nombre; // Usamos el nombre para la selección
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

    // Load all categories and types for the initial load
    async function loadCategoriesAndTypes() {
        try {
            const response = await fetch('./php/get_categories_and_types.php');
            const data = await response.json();

            if (data.status === 'success' && Array.isArray(data.categories)) {
                typeSelect.innerHTML = '<option value="">Seleccionar</option>';
                categorySelect.innerHTML = '<option value="">Seleccionar</option>';

                typeSelect.innerHTML += '<option value="Ingreso">Ingreso</option><option value="Egreso">Egreso</option>';

                data.categories.forEach(category => {
                    const option = document.createElement("option");
                    option.value = category.nombre; // Usamos el nombre para la selección
                    option.textContent = category.nombre;
                    categorySelect.appendChild(option);
                });

                // Cargar opciones de filtrado de categorías
                const filterCategorySelect = document.getElementById('filter-category');
                filterCategorySelect.innerHTML = '<option value="">Todas</option>';
                data.categories.forEach(category => {
                    const option = document.createElement("option");
                    option.value = category.nombre;
                    option.textContent = category.nombre;
                    filterCategorySelect.appendChild(option);
                });

            } else {
                console.error('Error al cargar categorías y tipos:', data.error);
                categorySelect.innerHTML = '<option value="">Error al cargar categorías y tipos</option>';
            }
        } catch (error) {
            console.error('Error al cargar categorías y tipos:', error);
            categorySelect.innerHTML = '<option value="">Error al cargar categorías y tipos</option>';
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
                    option.value = patient.nombre; // Usamos el nombre para la selección
                    option.textContent = patient.nombre;
                    patientSelect.appendChild(option);
                });
            } else {
                throw new Error('Formato de datos no válido');
            }
        } catch (error) {
            console.error('Error al cargar pacientes:', error);
            patientSelect.innerHTML = '<option value="">Error al cargar pacientes</option>';
        }
    }

    // Show modal with message
    function showModal(title, message) {
        const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
        document.getElementById('messageModalLabel').innerText = title;
        document.getElementById('modalMessage').innerText = message;
        messageModal.show();
    }

    // Variables to store chart instances
    let incomeExpenseChart;
    let expenseCategoryChart;
    let incomeCategoryChart;

    // Fetch transactions based on filters
    function fetchTransactions(page = 1) {
        const filterMonthElement = document.getElementById('filter-month');
        const filterTypeElement = document.getElementById('filter-type');
        const filterCategoryElement = document.getElementById('filter-category');

        if (!filterMonthElement || !filterTypeElement || !filterCategoryElement) {
            console.error('Elementos de filtro no encontrados en el DOM.');
            return;
        }

        const filterMonth = filterMonthElement.value || new Date().toISOString().slice(0, 7);
        const filterType = filterTypeElement.value;
        const filterCategory = filterCategoryElement.value;

        const params = new URLSearchParams({
            month: filterMonth,
            type: filterType,
            category: filterCategory,
            page: page
        });

        fetch(`./php/get_transactions.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                console.error('Error data:', data.error);
                showModal('Error', data.error);
            } else {
                renderTable(data.transactions);
                renderCharts(data.transactions);
                renderPagination(data.page, data.pages);
                updateBalance(data.transactions);
            }
        })
        .catch(error => {
            console.error('Error fetching transactions:', error);
            showModal('Error', 'Error fetching transactions: ' + error.message);
        });
    }

    // Render transactions table
    function renderTable(data) {
        const tbody = document.getElementById('transactions-table').querySelector('tbody');
        if (!tbody) return;

        tbody.innerHTML = '';

        data.forEach(transaction => {
            const patientDetail = transaction.categoria ? transaction.categoria.includes('Paciente Mensual') || transaction.categoria.includes('Paciente Semanal') ? transaction.paciente : '' : '';
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${transaction.fecha}</td>
                <td>${transaction.tipo}</td>
                <td>${transaction.categoria}</td>
                <td>${parseFloat(transaction.monto).toLocaleString('es-UY', { style: 'currency', currency: 'UYU' })}</td>
                <td>${transaction.comentarios || ''}</td>
                <td>${patientDetail}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Render charts
    function renderCharts(data) {
        const ctxIncomeExpense = document.getElementById('income-expense-chart').getContext('2d');
        const ctxExpenseCategory = document.getElementById('expense-category-chart').getContext('2d');
        const ctxIncomeCategory = document.getElementById('income-category-chart').getContext('2d');

        const totalIncome = data.filter(item => item.tipo === 'Ingreso').reduce((sum, item) => sum + parseFloat(item.monto), 0);
        const totalExpense = data.filter(item => item.tipo === 'Egreso').reduce((sum, item) => sum + parseFloat(item.monto), 0);

        const expenseCategories = [...new Set(data.filter(item => item.tipo === 'Egreso').map(item => item.categoria))];
        const expenseAmounts = expenseCategories.map(category => data.filter(item => item.tipo === 'Egreso' && item.categoria === category).reduce((sum, item) => sum + parseFloat(item.monto), 0));

        const incomeCategories = [...new Set(data.filter(item => item.tipo === 'Ingreso').map(item => item.categoria))];
        const incomeAmounts = incomeCategories.map(category => data.filter(item => item.tipo === 'Ingreso' && item.categoria === category).reduce((sum, item) => sum + parseFloat(item.monto), 0));

        if (incomeExpenseChart) incomeExpenseChart.destroy();
        if (expenseCategoryChart) expenseCategoryChart.destroy();
        if (incomeCategoryChart) incomeCategoryChart.destroy();

        if (totalIncome > 0 || totalExpense > 0) {
            incomeExpenseChart = new Chart(ctxIncomeExpense, {
                type: 'doughnut',
                data: {
                    labels: ['Ingresos', 'Egresos'],
                    datasets: [{
                        label: 'Ingresos vs Egresos',
                        data: [totalIncome, totalExpense],
                        backgroundColor: ['#36A2EB', '#FF6384'],
                        hoverBackgroundColor: ['#36A2EB80', '#FF638480'],
                        borderColor: ['#fff', '#fff'],
                        borderWidth: 2,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.raw.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        if (expenseCategories.length > 0 && expenseAmounts.length > 0) {
            expenseCategoryChart = new Chart(ctxExpenseCategory, {
                type: 'pie',
                data: {
                    labels: expenseCategories,
                    datasets: [{
                        label: 'Gastos por categoría',
                        data: expenseAmounts,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#FF5733', '#33FF57', '#5733FF'],
                        hoverBackgroundColor: ['#FF638480', '#36A2EB80', '#FFCE5680', '#FF573380', '#33FF5780', '#5733FF80'],
                        borderColor: ['#fff', '#fff', '#fff', '#fff', '#fff', '#fff'],
                        borderWidth: 2,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.raw.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        if (incomeCategories.length > 0 && incomeAmounts.length > 0) {
            incomeCategoryChart = new Chart(ctxIncomeCategory, {
                type: 'pie',
                data: {
                    labels: incomeCategories,
                    datasets: [{
                        label: 'Ingresos por categoría',
                        data: incomeAmounts,
                        backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#FF5733', '#33FF57', '#5733FF'],
                        hoverBackgroundColor: ['#36A2EB80', '#FF638480', '#FFCE5680', '#FF573380', '#33FF5780', '#5733FF80'],
                        borderColor: ['#fff', '#fff', '#fff', '#fff', '#fff', '#fff'],
                        borderWidth: 2,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.raw.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Update balance information
    function updateBalance(transactions) {
        const totalIncome = transactions.filter(item => item.tipo === 'Ingreso').reduce((sum, item) => sum + parseFloat(item.monto), 0);
        const totalExpense = transactions.filter(item => item.tipo === 'Egreso').reduce((sum, item) => sum + parseFloat(item.monto), 0);
        const totalSavings = transactions.filter(item => item.categoria === 'Ahorros' && item.tipo === 'Ingreso').reduce((sum, item) => sum + parseFloat(item.monto), 0);

        const balance = totalIncome - totalExpense;

        document.getElementById('total-income').innerText = totalIncome.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
        document.getElementById('total-expense').innerText = totalExpense.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
        document.getElementById('total-balance').innerText = balance.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
        document.getElementById('total-savings').innerText = totalSavings.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
    }

    // Render pagination
    function renderPagination(currentPage, totalPages) {
        const paginationContainer = document.getElementById('pagination-container');
        if (!paginationContainer) return;

        paginationContainer.innerHTML = '';

        for (let page = 1; page <= totalPages; page++) {
            const pageButton = document.createElement('button');
            pageButton.className = 'btn btn-secondary';
            pageButton.innerText = page;
            pageButton.disabled = page === currentPage;
            pageButton.addEventListener('click', () => fetchTransactions(page));
            paginationContainer.appendChild(pageButton);
        }
    }

    // Initial load
    loadCategoriesAndTypes();
    fetchTransactions();
});