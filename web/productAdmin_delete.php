<!DOCTYPE html>
<html lang="en">
<?php
require '../helperFile/ProductMaintenance_base.php';

// Get product ID from URL
$productId = $_GET['id'] ?? null;

if ($productId) {
    // Retrieve product details
    $sql = 'SELECT * FROM product WHERE product_id = :productId';
    $stmt = $_db->prepare($sql);
    $stmt->execute(['productId' => $productId]);
    $product = $stmt->fetch();

    // Check if the product exists
    if ($product) {
        $GLOBALS['name'] = $product->product_name;
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
    // Begin transaction
    $_db->beginTransaction();

    try {
        // Delete the product from the `product_img` table first
        $sqlImage = 'DELETE FROM product_img WHERE product_id = :productId';
        $stmtImage = $_db->prepare($sqlImage);
        $stmtImage->execute(['productId' => $productId]);

        // Delete the product from the `product` table
        $sqlProduct = 'DELETE FROM product WHERE product_id = :productId';
        $stmtProduct = $_db->prepare($sqlProduct);
        $stmtProduct->execute(['productId' => $productId]);

        // Commit the transaction
        $_db->commit();

        // Redirect back to product list after deletion
        header('Location: productAdmin_list.php');
        exit();
    } catch (Exception $e) {
        // Rollback in case of an error
        $_db->rollBack();
        echo 'Failed to delete product: ' . $e->getMessage();
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/productAdmin_add.css">
    <title>Delete Product</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="productAdmin_list.php" class="back-arrow"><img src="../image/back-arrow.png" alt="Back"></a>
            <script src="../js/productAdmint_jsscript.js"></script>
            <div class="title">Delete Product</div>
        </div>

        <div class="content">
            <form method="post" action="">
                <div class="product-details">

                    <div class="input-box">
                        <label class="details" for="name">Product Name</label>
                        <?= html_text('name', 'maxlength="100" disabled="disabled"') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="price">Price</label>
                        <?= html_number('price', 'min="0" step="0.01" disabled="disabled"') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="description">Description</label>
                        <?= html_textArea('description', 'rows="5" cols="45" disabled="disabled"') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="stock">Product Stock</label>
                        <?= html_number('stock', 'min="0" step="1" disabled="disabled"') ?>
                    </div>
                    
                </div>

                <!-- Buttons -->
                <div class="button">
                    <input type="submit" name="submit" value="Delete">
                </div>

            </form>
        </div>
    </div>

</body>

</html>
