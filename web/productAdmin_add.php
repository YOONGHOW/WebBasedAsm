<!DOCTYPE html>
<html lang="en">

<?php
// Connect to the database
require '../helperFile/ProductMaintenance_base.php';

// Generate unique product_id
$product_id = getNextId($_db, 'P', 'product_id', 'product');

// Generate unique category_id
$category_id = getNextId($_db, 'C', 'category_id', 'category');

// Get the current date
$current_date = date('Y-m-d');

// Default status
$status = "Y";
?>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/productAdmin_add.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
</head>

<body>

    <div class="container">

        <div class="header">
            <a href="../index.php" class="back-arrow"><img src="../image/back-arrow.png" alt="Back"></a>
            <div class="title">Add New Product</div>
        </div>

        <!-- User input field -->
        <div class="content">
            <!-- Update form to allow file uploads with enctype -->
            <form method="POST" action="addProductValidation.php" enctype="multipart/form-data">
                <?= html_hidden('product_id', $product_id) ?>
                <?= html_hidden('category_id', $category_id) ?>
                <?= html_hidden('date_added', $current_date) ?>
                <?= html_hidden('status', $status) ?>

                <div class="product-details">
                    <div class="input-box">
                        <label class="details" for="name">Product Name</label>
                        <?= html_text('name', 'maxlength="100" ') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="price">Price</label>
                        <?= html_number('price', 'min="0" step="0.01" ') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="description">Description</label>
                        <?= html_textArea('description', 'rows="5" cols="45" ') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="stock">Product Stock</label>
                        <?= html_number('stock', 'min="0" step="1" ') ?>
                    </div>

                    <!-- upload photo: -->
                    <div class="input-box">
                        <label class="details" for="product_images">Product Images</label>
                        <?= html_file('product_images') ?>
                    </div>

                </div>

                <!-- Buttons -->
                <div class="button">
                    <input type="submit" name="submit" value="Update">
                </div>
            </form>
        </div>
    </div>

</body>

</html>