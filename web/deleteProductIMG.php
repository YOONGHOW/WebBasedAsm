<?php
require '../helperFile/ProductMaintenance_base.php';

if (is_post()) {
    // Get image ID and image source from the POST request
    $imageId = $_POST['product_IMG_id'] ?? null;
    $imageSource = $_POST['image_source'] ?? null;
    $productId = $_POST['product_id'] ?? null;

    if ($imageId && $imageSource) {
        // Delete the image from the database
        $sql = 'DELETE FROM product_img WHERE product_IMG_id = :id';
        $stmt = $_db->prepare($sql);
        $stmt->bindParam(':id', $imageId);
        $stmt->execute();

        // Delete the image file from the server
        $imagePath = '../' . $imageSource; // Assuming the image source path is relative to the base folder
        if (file_exists($imagePath)) {
            unlink($imagePath); // Remove the file from the server
        }

        // Redirect back to the image update page after deletion
        header('Location: productAdmin_image.php?id=' . ($productId));
        exit();
    }
}
