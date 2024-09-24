document.addEventListener('DOMContentLoaded', function() {
    // Low stock alert feature
    products.forEach(product => {
        if (parseInt(product.product_stock) < 10) {
            alert(`Warning: Low stock detected for Product ID: ${product.product_id}, Stock: ${product.product_stock}`);
        }
    });

    const form = document.querySelector('form');
    const addButton = document.querySelector('#addButton'); // Add button
    const deleteButton = document.querySelector('#deleteButton'); // Delete button
    const deleteButtonImage = document.querySelector('#deleteButtonImage'); // Delete image button
    const updateButton = document.querySelector('#updateButton'); // Update button
    const uploadImageButton = document.querySelector('#uploadImageButton'); // Update image button
    const updateCategoryButton = document.querySelector('#updateCategoryButton'); // Update category button
    const DeleteCategoryButton = document.querySelector('#DeleteCategoryButton'); // Delete category button
    const addCategoryButton = document.querySelector('#addCategoryButton'); // Add category button

    form.addEventListener('submit', function(event) {
        let confirmationMessage = '';

        // Check which button was clicked
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
        } else if(event.submitter === updateCategoryButton){
            confirmationMessage = 'Are you sure you want to update this Category?';
        } else if(event.submitter === DeleteCategoryButton){
            confirmationMessage = 'Are you sure you want to delete this Category?';
        } else if(event.submitter === addCategoryButton){
            confirmationMessage = 'Are you sure you want to add this Category?';
        }
        
        if (!confirm(confirmationMessage)) {
            event.preventDefault(); // Prevent form submission if the user cancels
        }
    });
});
