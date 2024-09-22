<!DOCTYPE html>
<html lang="en">
<?php
require '../helperFile/ProductMaintenance_base.php';

// Get product ID from URL
$productId = $_GET['id'] ?? null;

// Initialize an empty array for product images
$productImages = [];

if ($productId) {
    // Fetch product images from the database
    $sql = 'SELECT * FROM product_img WHERE product_id = :product_id';
    $stmt = $_db->prepare($sql);
    $stmt->bindParam(':product_id', $productId);
    $stmt->execute();
    $productImages = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative array
}

if (is_post()) {
    // Image upload validation
    $imageResult = checkImageFile('product_images');
    if (isset($imageResult['error'])) {
        $_err['product_images'] = $imageResult['error'];
    }

    if (empty($_err)) {
        // If the image was uploaded, save the image details to the database
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
                $stmt_img->bindParam(':product_id', $productId);
                $stmt_img->bindParam(':image_name', $imageData['image_name']);
                $stmt_img->bindParam(':image_destination', $imageData['image_destination']);
                $stmt_img->execute();
            }

            // Redirect to refresh the page and display newly uploaded images
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        }
    }
}


?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/productAdmin_add.css">
    <script src="../js/productAdmin_jsscript.js"></script>
    <title>Update Product Photo</title>

    <style>
        .image-container {
            display: flex;
            flex-wrap: wrap;
            /* Allow wrapping to the next line */
            gap: 10px;
            /* Space between images */
            justify-content: flex-start;
            /* Align items to the start */
        }

        .image-item {
            position: relative;
            /* Make this the positioning context for the button */
            width: calc(25% - 10px);
            /* Set width for each image */
        }

        .image-item img {
            display: block;
            width: 100%;
            /* Ensure the image takes up the full container width */
            height: auto;
            object-fit: cover;
            /* Ensure the images maintain aspect ratio */
        }

        .image-item button {
            position: absolute;
            top: 5px;
            right: 5px;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .image-item button:hover {
            background-color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="productAdmin_list.php" class="back-arrow"><img src="../image/back-arrow.png" alt="Back"></a>
            <div class="title">Update Product Photo</div>
        </div>

        <div class="content">
            <!-- Container for images -->
            <div class="image-container">
                <!-- Loop through and display each product image -->
                <?php foreach ($productImages as $s): ?>
                    <div class="image-item">
                        <img src="<?= htmlspecialchars($s['product_IMG_source']) ?>" alt="Product Image">

                        <form action="deleteProductIMG.php" method="post">
                            <?= html_hidden('product_IMG_id', $s['product_IMG_id']) ?>
                            <?= html_hidden('product_id', $productId) ?>
                            <?= html_hidden('image_source', $s['product_IMG_source']) ?>
                            <button type="submit" id="deleteButtonImage">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <br />
            <form action="" method="post" enctype="multipart/form-data">
                <div class="product-details">
                    <div class="input-box">
                        <label class="details" for="product_images">Product Images</label>
                        <?= html_file('product_images') ?>
                        <?= err('product_images') ?>
                    </div>
                </div>
                <div class="button">
                    <input type="submit" id="uploadImageButton" name="submit" value="UPLOAD IMAGE">
                </div>
            </form>
        </div>
    </div>

</body>

</html>