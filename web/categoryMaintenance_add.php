<!DOCTYPE html>
<html lang="en">
<?php
require '../helperFile/ProductMaintenance_base.php';


// Error array
$_err = [];

$category_id = getNextId($_db, 'C', 'category_id', 'category');

if(is_post()){

    //store the data post by user
    $name = $_POST['name'];
    $description = $_POST['description'];

    if(checkProductName($name)){
        $_err['name'] = checkProductName($name);
    }

    if(checkDescription($description)){
        $_err['description'] = checkDescription($description);
    }

    if(empty($_err)){
        //insert data into category table
        $sql = "INSERT INTO category(category_id,category_name,category_description) VALUES(:category_id,:category_name,:category_description)";
        $stmt = $_db->prepare($sql);
        $stmt->bindParam('category_id',$category_id);
        $stmt->bindParam('category_name',$name);
        $stmt->bindParam('category_description',$description);
        $stmt->execute();

        header('Location: categoryMaintenance_list.php');
        exit();

    }
}


?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/productAdmin_add.css">
    <script src="../js/productAdmin_jsscript.js"></script>
    <title>Add New Category Information</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="categoryMaintenance_list.php" class="back-arrow"><img src="../image/back-arrow.png" alt="Back"></a>
            <div class="title">Add New Category Information</div>
        </div>

        <div class="content">
            <form method="post" action="">
                <div class="product-details">

                <?=html_hidden('category_id', $category_id) ?>

                    <div class="input-box">
                        <label class="details" for="name">Category Name</label>
                        <?= html_text('name', 'minlength="5" maxlength="100" required') ?>
                        <?= err('name') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="description">Category Description</label>
                        <?= html_textArea('description', 'rows="5" cols="45" required') ?>
                        <?= err('description') ?>
                    </div>

                </div>

                <div>
                <!-- Buttons -->
                <div class="button">
                    <input type="submit" id="addCategoryButton" name="submit" value="Add Category">
                </div>

            </form>
        </div>
    </div>

</body>

</html>