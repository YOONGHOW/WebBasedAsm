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

    //product exist
    if ($product) {
        $GLOBALS['name'] = $product->product_name;
        $GLOBALS['category'] = $product->category_id;
        $GLOBALS['price'] = $product->product_price;
        $GLOBALS['stock'] = $product->product_stock;
        $GLOBALS['description'] = $product->product_description;
    } else {
        //if users change id in url display this
        echo 'Product not found';
        exit();
    }
} else {
    //if users modify product id in url display this
    echo 'Invalid product ID';
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    // SQL query to update product details
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
    header('Location: product_list.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/productAdmin_add.css">
    <title>Update Product Information</title>
</head>

<body>


</body>

</html>
