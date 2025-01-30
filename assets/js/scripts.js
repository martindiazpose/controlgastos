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
    const filterTypeElement = document.getElementById('filter-type');
    const paginationContainer = document.getElementById('pagination-container');

    // Asegúrate de que los elementos existen antes de agregar event listeners
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
                    patientSelect.innerHTML = "";
                }
            } else {
                subcategoryField.style.display = 'none';
                subcategorySelect.innerHTML = "";
            }
        });
    }

    // Load categories by type
    async function loadCategoriesByType(type) {
        try {
            const response = await fetch(`./php/get_categories_and_types.php?type=${type}`);
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
            const response = await fetch(`./php/get_subcategories.php?category_id=${categoryId}`);
            // Verifica si la respuesta es correcta
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
            const response = await fetch('./php/get_categories_and_types.php');
            // Verifica si la respuesta es correcta
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

                // Cargar opciones de filtrado de categorías si existen en el DOM
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
        const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
        document.getElementById('messageModalLabel').innerText = title;
        document.getElementById('modalMessage').innerText = message;
        messageModal.show();
    }

    // Variables to store chart instances
   // let incomeExpenseChart;
    //let expenseCategoryChart;
    //let incomeCategoryChart;
    //let subcategoryChart;

// Variables para almacenar las instancias de los gráficos
let incomeExpenseChart, expenseCategoryChart, incomeCategoryChart, gastosFijosSubcategoryChart, comidaSubcategoryChart;

// Fetch transactions based on filters
function fetchTransactions(page = 1) {
    if (!filterMonthElement || !filterTypeElement || !filterCategorySelect) {
        console.error('Elementos de filtro no encontrados en el DOM.');
        return;
    }

    const filterMonth = filterMonthElement.value || new Date().toISOString().slice(0, 7);
    const filterType = filterTypeElement.value;
    const filterCategory = filterCategorySelect.value;

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
            console.log(data.transactions); // Verifica que los datos están llegando correctamente
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
            <td>${transaction.categoria || ''}</td>
            <td>${transaction.subcategoria || ''}</td>
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
    const ctxGastosFijosSubcategory = document.getElementById('subcategory-gastos-fijos-chart').getContext('2d');
    const ctxComidaSubcategory = document.getElementById('subcategory-comida-chart').getContext('2d');

    const totalIncome = data.filter(item => item.tipo === 'Ingreso').reduce((sum, item) => sum + parseFloat(item.monto), 0);
    const totalExpense = data.filter(item => item.tipo === 'Egreso').reduce((sum, item) => sum + parseFloat(item.monto), 0);

    const expenseCategories = [...new Set(data.filter(item => item.tipo === 'Egreso').map(item => item.categoria))];
    const expenseAmounts = expenseCategories.map(category => data.filter(item => item.tipo === 'Egreso' && item.categoria === category).reduce((sum, item) => sum + parseFloat(item.monto), 0));

    const incomeCategories = [...new Set(data.filter(item => item.tipo === 'Ingreso').map(item => item.categoria))];
    const incomeAmounts = incomeCategories.map(category => data.filter(item => item.tipo === 'Ingreso' && item.categoria === category).reduce((sum, item) => sum + parseFloat(item.monto), 0));

    const gastosFijosSubcategories = [...new Set(data.filter(item => item.categoria === 'Gastos Fijos' && item.subcategoria).map(item => item.subcategoria))];
    const gastosFijosSubcategoryAmounts = gastosFijosSubcategories.map(subcategory => data.filter(item => item.categoria === 'Gastos Fijos' && item.subcategoria === subcategory).reduce((sum, item) => sum + parseFloat(item.monto), 0));

    const comidaSubcategories = [...new Set(data.filter(item => item.categoria === 'Comida' && item.subcategoria).map(item => item.subcategoria))];
    const comidaSubcategoryAmounts = comidaSubcategories.map(subcategory => data.filter(item => item.categoria === 'Comida' && item.subcategoria === subcategory).reduce((sum, item) => sum + parseFloat(item.monto), 0));

    // Destroy previous charts if they exist
    if (incomeExpenseChart) incomeExpenseChart.destroy();
    if (expenseCategoryChart) expenseCategoryChart.destroy();
    if (incomeCategoryChart) incomeCategoryChart.destroy();
    if (gastosFijosSubcategoryChart) gastosFijosSubcategoryChart.destroy();
    if (comidaSubcategoryChart) comidaSubcategoryChart.destroy();

    // Create new charts
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
                    title: {
                        display: true,
                        text: 'Ingresos vs Egresos'
                    },
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
                    title: {
                        display: true,
                        text: 'Gastos por Categoría'
                    },
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
                    title: {
                        display: true,
                        text: 'Ingresos por Categoría'
                    },
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

    if (gastosFijosSubcategories.length > 0 && gastosFijosSubcategoryAmounts.length > 0) {
        gastosFijosSubcategoryChart = new Chart(ctxGastosFijosSubcategory, {
            type: 'pie',
            data: {
                labels: gastosFijosSubcategories,
                datasets: [{
                    label: 'Gastos Fijos por subcategoría',
                    data: gastosFijosSubcategoryAmounts,
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
                    title: {
                        display: true,
                        text: 'Gastos Fijos por Subcategoría'
                    },
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

    if (comidaSubcategories.length > 0 && comidaSubcategoryAmounts.length > 0) {
        comidaSubcategoryChart = new Chart(ctxComidaSubcategory, {
            type: 'pie',
            data: {
                labels: comidaSubcategories,
                datasets: [{
                    label: 'Comida por subcategoría',
                    data: comidaSubcategoryAmounts,
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
                    title: {
                        display: true,
                        text: 'Comida por Subcategoría'
                    },
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
async function fetchExchangeRate() {
    try {
        const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD'); // URL de la API para obtener la cotización
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        return data.rates.UYU; // Retornar la cotización del dólar en pesos uruguayos
    } catch (error) {
        console.error('Error fetching exchange rate:', error);
    }
}

async function updateBalance(transactions) {
    const exchangeRate = await fetchExchangeRate();
    if (!exchangeRate) {
        showModal('Error', 'No se pudo obtener la cotización del dólar.');
        return;
    }

    // Total ingresos excluyendo ingresos a ahorros
    const totalIncome = transactions.filter(item => item.tipo === 'Ingreso' && item.categoria !== 'Ahorros').reduce((sum, item) => sum + parseFloat(item.monto), 0);
    // Total ingresos a ahorros que se deben restar de los ingresos totales
    const totalIncomeToSavings = transactions.filter(item => item.categoria === 'Ahorros' && item.tipo === 'Ingreso').reduce((sum, item) => sum + parseFloat(item.monto), 0);
    // Total egresos que no sean a ahorros
    const totalExpense = transactions.filter(item => item.tipo === 'Egreso' && item.categoria !== 'Ahorros').reduce((sum, item) => sum + parseFloat(item.monto), 0);
    // Total egresos a ahorros que se deben sumar a los ahorros
    const totalSavingsExpense = transactions.filter(item => item.categoria === 'Ahorros' && item.tipo === 'Egreso').reduce((sum, item) => sum + parseFloat(item.monto), 0);

    // Ajustar balances: restar los ingresos a ahorros del total de ingresos
    const adjustedTotalIncome = totalIncome - totalIncomeToSavings;
    const savings = totalSavingsExpense - totalIncomeToSavings;
    const savingsInDollars = savings / exchangeRate;

    // Actualizar los elementos del DOM si existen
    const totalIncomeElement = document.getElementById('total-income');
    if (totalIncomeElement) {
        totalIncomeElement.innerText = adjustedTotalIncome.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
    }

    const totalExpenseElement = document.getElementById('total-expense');
    if (totalExpenseElement) {
        totalExpenseElement.innerText = totalExpense.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
    }

    const totalBalanceElement = document.getElementById('total-balance');
    if (totalBalanceElement) {
        totalBalanceElement.innerText = (adjustedTotalIncome - totalExpense).toLocaleString('es-UY', { style: 'currency', currency: 'UYU' });
    }

    const totalSavingsElement = document.getElementById('total-savings');
    if (totalSavingsElement) {
        totalSavingsElement.innerText = `${savings.toLocaleString('es-UY', { style: 'currency', currency: 'UYU' })} $UY`;
    }

    const totalSavingsUsdElement = document.getElementById('total-savings-usd');
    if (totalSavingsUsdElement) {
        totalSavingsUsdElement.innerText = `${savingsInDollars.toLocaleString('en-US', { style: 'currency', currency: 'USD' })} USD`;
    }
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

// Load initial data
function loadCategoriesAndTypes() {
    // Load categories and types dynamically if needed
}

// Initial load
loadCategoriesAndTypes();
fetchTransactions();})