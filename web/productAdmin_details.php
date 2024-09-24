<!DOCTYPE html>
<html lang="en">
<?php
require '../helperFile/ProductMaintenance_base.php';

// Get product ID from URL
$productId = $_GET['id'] ?? null;

$productImages = [];

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
        $GLOBALS['date'] = $product->product_create_date;

        $sqlImage = 'SELECT * FROM product_img WHERE product_id = :product_id';
        $stmtImage = $_db->prepare($sqlImage);
        $stmtImage->bindParam(':product_id', $productId);
        $stmtImage->execute();
        $productImages = $stmtImage->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo 'Product not found';
        exit();
    }
} else {
    echo 'Invalid product ID';
    exit();
}

?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/productAdmin_add.css">
    <link rel="stylesheet" href="../css/productAdmin_details.css">
    <title>Product Details</title>

    <style>

    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="productAdmin_list.php" class="back-arrow"><img src="../image/back-arrow.png" alt="Back"></a>
            <div class="title">Product Details</div>
        </div>

        <div class="content">

            <!-- Container for images -->
            <div class="image-container">
                <!-- Loop through and display each product image -->
                <?php foreach ($productImages as $s): ?>
                    <div class="image-item">
                        <img src="<?= htmlspecialchars($s['product_IMG_source']) ?>" alt="Product Image">

                        <?= html_hidden('product_IMG_id', $s['product_IMG_id']) ?>
                        <?= html_hidden('product_id', $productId) ?>
                        <?= html_hidden('image_source', $s['product_IMG_source']) ?>
                    </div>
                <?php endforeach; ?>
            </div>

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
                    <label class="details" for="stock">Product Stock</label>
                    <?= html_number('stock', 'min="0" step="1" disabled="disabled"') ?>
                </div>

                <div class="input-box">
                    <label class="details" for="category">Category</label>
                    <?= displayCategoryInText() ?>
                </div>

                <div class="input-box">
                    <label class="details" for="date">Product Name</label>
                    <?= html_text('date', 'maxlength="100" disabled="disabled"') ?>
                </div>                

                <div class="input-box">
                    <label class="details" for="description">Description</label>
                    <?= html_textArea('description', 'rows="5" cols="45" disabled="disabled"') ?>
                </div>

            </div>

        </div>

    </div>
    </div>

</body>

</html>