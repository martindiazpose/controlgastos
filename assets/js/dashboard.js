document.addEventListener('DOMContentLoaded', function () {
    const confirmDeleteModal = document.getElementById('confirmDeleteModal');
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    const viewButtons = document.querySelectorAll('.view-transaction-btn');
    const deleteButtons = document.querySelectorAll('.delete-transaction-btn');
    let transactionToDelete = null;

    if (confirmDeleteModal && confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', function () {
            if (transactionToDelete) {
                deleteTransaction(transactionToDelete);
                transactionToDelete = null;
            }
        });
    }

    if (viewButtons.length > 0) {
        viewButtons.forEach(button => {
            button.addEventListener('click', function () {
                const transactionId = this.getAttribute('data-id');
                const transaction = getTransactionById(transactionId);
                showTransactionDetails(transaction);
            });
        });
    }

    if (deleteButtons.length > 0) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const transactionId = this.getAttribute('data-id');
                showDeleteConfirmationModal(transactionId);
            });
        });
    }

    function deleteTransaction(id) {
        console.log(`Eliminar transacción con ID: ${id}`);
        fetch('./php/delete_transaction.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log(`Transacción con ID ${id} eliminada correctamente.`);
                    location.reload();
                } else {
                    console.error(`Error al eliminar la transacción:`, data.error);
                }
            })
            .catch(error => console.error(`Error al eliminar la transacción:`, error));
    }

    function getTransactionById(id) {
        // Implementa la lógica para obtener la transacción por ID
    }

    function showTransactionDetails(transaction) {
        // Implementa la lógica para mostrar los detalles de la transacción
    }

    function showDeleteConfirmationModal(id) {
        transactionToDelete = id;
        const modal = new bootstrap.Modal(confirmDeleteModal);
        modal.show();
    }
});