<!DOCTYPE html>
<html lang="en">
<?php
require '../helperFile/ProductMaintenance_base.php';

// Get product ID from URL
$categoryId = $_GET['id'] ?? null;

if($categoryId){
    $sql = "SELECT * FROM category WHERE category_id = :category_id";
    $stmt = $_db->prepare($sql);
    $stmt->execute([':category_id' => $categoryId]);
    $category = $stmt->fetch();
    
    if($category){
        $GLOBALS['name'] = $category->category_name;
        $GLOBALS['description'] = $category->category_description;
    }else{
        echo "Category not found";
        exit();
    }

}else{
    echo 'Invalid Category ID';
    exit();
}

if(is_post()){
    
    $sql = "DELETE FROM category WHERE category_id = :category_id";
    $stmt = $_db->prepare($sql);
    $stmt->execute([':category_id' => $categoryId]);

    header('Location: categoryMaintenance_list.php');
    exit();
    }


?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/productAdmin_add.css">
    <script src="../js/productAdmin_jsscript.js"></script>
    <title>Delete Category Information</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="categoryMaintenance_list.php" class="back-arrow"><img src="../image/back-arrow.png" alt="Back"></a>
            <div class="title">Delete Category Information</div>
        </div>

        <div class="content">
            <form method="post" action="">
                <div class="product-details">

                    <div class="input-box">
                        <label class="details" for="name">Category Name</label>
                        <?= html_text('name', 'minlength="5" maxlength="100" disabled="disabled" ') ?>
                        <?= err('name') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="description">Category Description</label>
                        <?= html_textArea('description', 'rows="5" cols="45" disabled="disabled"') ?>
                        <?= err('description') ?>
                    </div>

                </div>

                <div>
                <!-- Buttons -->
                <div class="button">
                    <input type="submit" id="DeleteCategoryButton" name="submit" value="Delete Category">
                </div>

            </form>
        </div>
    </div>

</body>

</html>