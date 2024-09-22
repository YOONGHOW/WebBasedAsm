<?php
// Connect to the database
require '../helperFile/ProductMaintenance_base.php';

// Retrieve data from the form
$name = $_POST['name'];
$price = $_POST['price'];
$description = $_POST['description'];
$stock = $_POST['stock'];
$product_id = $_POST['product_id'];    
$category_id = $_POST['category_id'];
$current_date = $_POST['date_added'];
$status = $_POST['status'];    


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

        // Check if there are any uploaded images
        if (!empty($_FILES['product_images']['name'][0])) {
            // Loop through each uploaded image
            foreach ($_FILES['product_images']['name'] as $index => $image_name) {
                $image_tmp_name = $_FILES['product_images']['tmp_name'][$index];
                $image_error = $_FILES['product_images']['error'][$index];
                
                // file no error run
                if ($image_error === 0) {

                    // generate filename
                    $image_name_new = uniqid('IMG_', true) . '.' . pathinfo($image_name, PATHINFO_EXTENSION);
                    $image_destination = '../image/' . $image_name_new;  

                    // Move the uploaded image to the destination folder
                    if (move_uploaded_file($image_tmp_name, $image_destination)) {

                        // generate image id
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
                        $stmt_img->bindParam(':image_name', $image_name_new);
                        $stmt_img->bindParam(':image_destination', $image_destination);
                        
                        $stmt_img->execute();
                    }
                }
            }
        }

        // Redirect with success status
        header('Location: productAdmin_add.php?status=success');
        exit();
    } else {
        // Redirect with error status if product insertion failed
        header('Location: productAdmin_add.php?status=error');
        exit();
    }
} catch (PDOException $e) {
    // Redirect with error status if there was a database error
    header('Location: productAdmin_add.php?status=error');
    exit();
}
?>
