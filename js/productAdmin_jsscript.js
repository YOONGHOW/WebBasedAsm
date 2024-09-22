document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const addButton = document.querySelector('#addButton'); // Add button
    const deleteButton = document.querySelector('#deleteButton'); // Delete button
    const deleteButtonImage = document.querySelector('#deleteButtonImage'); // Delete image button
    const updateButton = document.querySelector('#updateButton'); // update button
    const uploadImageButton = document.querySelector('#uploadImageButton'); // update image button

    form.addEventListener('submit', function(event) {
        let confirmationMessage = '';

        // check button users click
        if (event.submitter === addButton) {
            confirmationMessage = 'Are you sure you want to submit this form and add the product?';
        } else if (event.submitter === deleteButton) {
            confirmationMessage = 'Are you sure you want to delete this product?';
        } else if(event.submitter === updateButton){
            confirmationMessage = 'Are you sure you want to update this product?';
        } else if(event.submitter === deleteButtonImage){
            confirmationMessage = 'Are you sure you want to delete this Image?';
        } else if(event.submitter === uploadImageButton){
            confirmationMessage = 'Are you sure you want to update this Image?';
        }

        const confirmation = confirm(confirmationMessage);
        if (!confirmation) {
            event.preventDefault(); // Stop the form from submitting if the user cancels
        }
    });
});