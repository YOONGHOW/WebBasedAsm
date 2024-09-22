document.addEventListener('DOMContentLoaded', function() {
    const deleteForm = document.querySelector('form');

    deleteForm.addEventListener('submit', function(event) {
        const confirmation = confirm('Are you sure you want to delete this product?');
        if (!confirmation) {
            event.preventDefault(); // Prevent form submission if user cancels
        }
    });
});