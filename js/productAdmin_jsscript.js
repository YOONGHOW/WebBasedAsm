document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const addButton = document.querySelector('#addButton'); // Add button
    const deleteButton = document.querySelector('#deleteButton'); // Delete button
    const updateButton = document.querySelector('#updateButton'); // update button

    form.addEventListener('submit', function(event) {
        let confirmationMessage = '';

        // check button users click
        if (event.submitter === addButton) {
            confirmationMessage = 'Are you sure you want to submit this form and add the product?';
        } else if (event.submitter === deleteButton) {
            confirmationMessage = 'Are you sure you want to delete this product?';
        } else if(event.submitter === updateButton){
            confirmationMessage = 'Are you sure you want to update this product?';
        }

        const confirmation = confirm(confirmationMessage);
        if (!confirmation) {
            event.preventDefault(); // Stop the form from submitting if the user cancels
        }
    });
});