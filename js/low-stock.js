document.addEventListener('DOMContentLoaded', function() {
    // Low stock alert feature
    products.forEach(product => {
        if (parseInt(product.product_stock) < 10) {
            alert(`Warning: Low stock detected for Product ID: ${product.product_id}, Stock: ${product.product_stock}`);
        }
    });
});