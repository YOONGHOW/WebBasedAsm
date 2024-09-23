<!DOCTYPE html>
<html lang="en">

<?php
require '../helperFile/ProductMaintenance_base.php';

// generate id
$product_id = getNextId($_db, 'P', 'product_id', 'product');

// Get the current date
$current_date = date('Y-m-d');

// Default status
$status = "Y";

// Error array
$_err = [];

// Check if the form is submitted
if (is_post()) {
    // Retrieve data from the form
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $category_id = $_POST['category'] ?? '';

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

    // Image upload validation
    $imageResult = checkImageFile('product_images');
    if (isset($imageResult['error'])) {
        $_err['product_images'] = $imageResult['error'];
    }

    // Save product details to the database if no errors
    if (empty($_err)) {
        $sqlQuery = "INSERT INTO product (
            product_id, 
            category_id, 
            product_name, 
            product_price, 
            product_description, 
            product_stock, 
            product_create_date,
            product_status
        ) VALUES (
            :product_id, 
            :category_id, 
            :name, 
            :price, 
            :description, 
            :stock, 
            :current_date,
            :status
        )";
    
        try {
            $stmt = $_db->prepare($sqlQuery);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':stock', $stock);
            $stmt->bindParam(':current_date', $current_date);
            $stmt->bindParam(':status', $status);
    
            if ($stmt->execute()) {
                // If images were uploaded, save them to the database
                if (!isset($imageResult['error'])) {
                    foreach ($imageResult as $imageData) {
                        $image_id = getNextId($_db, 'IMG', 'product_IMG_id', 'product_img');
    
                        $sqlImage = "INSERT INTO product_img (
                            product_IMG_id, 
                            product_id, 
                            product_IMG_name, 
                            product_IMG_source
                        ) VALUES (
                            :image_id, 
                            :product_id, 
                            :image_name, 
                            :image_destination
                        )";
    
                        $stmt_img = $_db->prepare($sqlImage);
                        $stmt_img->bindParam(':image_id', $image_id);
                        $stmt_img->bindParam(':product_id', $product_id);
                        $stmt_img->bindParam(':image_name', $imageData['image_name']);
                        $stmt_img->bindParam(':image_destination', $imageData['image_destination']);
                        $stmt_img->execute();
                    }
                }
    
                header('Location: productAdmin_add.php?status=success');
                exit();
            } else {
                header('Location: productAdmin_add.php?status=error');
                exit();
            }
        } catch (PDOException $e) {
            header('Location: productAdmin_add.php?status=error');
            exit();
        }
    }
}
?>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/productAdmin_add.css">
    <script src="../js/productAdmin_jsscript.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
</head>

<body>

    <div class="container">

        <div class="header">
            <a href="../index.php" class="back-arrow"><img src="../image/back-arrow.png" alt="Back"></a>
            <div class="title">Add New Product</div>
        </div>

        <div class="content">
            <form method="POST" action="" enctype="multipart/form-data">
                <?= html_hidden('product_id', $product_id) ?>
                <?= html_hidden('date_added', $current_date) ?>
                <?= html_hidden('status', $status) ?>

                <div class="product-details">
                    <div class="input-box">
                        <label class="details" for="name">Product Name</label>
                        <?= html_text('name', 'maxlength="100" required') ?>
                        <?= err('name') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="category">Product Name</label>
                        <?= displayCategoryList() ?>
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
                        <label class="details" for="product_images">Product Images</label>
                        <?= html_file('product_images') ?>
                        <?= err('product_images') ?>
                    </div>

                </div>

                <div class="button">
                    <input type="submit" id="addButton" name="submit" value="ADD PRODUCT">
                </div>
            </form>
        </div>
    </div>

</body>

</html>