<!DOCTYPE html>
<html lang="en">
<?php
require '../helperFile/ProductMaintenance_base.php';

// Get product ID from URL
$productId = $_GET['id'] ?? null;

// Error array
$_err = [];

if ($productId) {
    // Retrieve product details
    $sql = 'SELECT * FROM product WHERE product_id = :productId';
    $stmt = $_db->prepare($sql);
    $stmt->execute(['productId' => $productId]);
    $product = $stmt->fetch();

    // Check if the product exists
    if ($product) {
        $GLOBALS['name'] = $product->product_name;
        $GLOBALS['category'] = $product->category_id;
        $GLOBALS['price'] = $product->product_price;
        $GLOBALS['stock'] = $product->product_stock;
        $GLOBALS['description'] = $product->product_description;
    } else {
        echo 'Product not found';
        exit();
    }
} else {
    echo 'Invalid product ID';
    exit();
}

// Process form submission
if (is_post()) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    //validation
    if (checkProductName($name) !== null) {
        $_err['name'] = checkProductName($name);
    }

    if (checkProductPrice($price) !== null) {
        $_err['price'] = checkProductPrice($price);
    }

    if (checkProductStock($stock) !== null) {
        $_err['stock'] = checkProductStock($stock);
    }

    if (checkDescription($description) !== null) {
        $_err['description'] = checkDescription($description);
    }


    if (empty($_err)) {
        // SQL query to update product details and image
        $sql = 'UPDATE product 
            SET product_name = :name,
            category_id = :category, 
            product_price = :price, 
            product_stock = :stock, 
            product_description = :description 
            WHERE product_id = :productId';

        $stmt = $_db->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'category' => $category,
            'price' => $price,
            'stock' => $stock,
            'description' => $description,
            'productId' => $productId
        ]);

        // Redirect back to product list after update
        header('Location: productAdmin_list.php');
        exit();
    }
}
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/productAdmin_add.css">
    <script src="../js/productAdmin_jsscript.js"></script>
    <title>Update Product Information</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="productAdmin_list.php" class="back-arrow"><img src="../image/back-arrow.png" alt="Back"></a>
            <div class="title">Update Product Information</div>
        </div>

        <div class="content">
            <form method="post" action="">
                <div class="product-details">

                    <div class="input-box">
                        <label class="details" for="name">Product Name</label>
                        <?= html_text('name', 'minlength="5" maxlength="100" required') ?>
                        <?= err('name') ?>
                    </div>



                    <div class="input-box">
                        <label class="details" for="price">Price</label>
                        <?= html_number('price', 'min="0" step="0.01" required') ?>
                        <?= err('price') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="description">Description</label>
                        <?= html_textArea('description', 'rows="5" cols="45" required') ?>
                        <?= err('description') ?>
                    </div>


                    <div class="input-box">
                        <label class="details" for="stock">Product Stock</label>
                        <?= html_number('stock', 'min="0" step="1" required') ?>
                        <?= err('stock') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="category">Category</label>
                        <?= displayCategoryList() ?>
                    </div>

                </div>

                <!-- Buttons -->
                <div class="button">
                    <input type="submit" id="updateButton" name="submit" value="Update">
                </div>

            </form>
        </div>
    </div>

</body>

</html>