<?php include "header.php"; ?>
<?php
global $_user;

if($_user == null){
    echo "<script>alert('You must login as member first')
    window.location.href = 'home.php';
    </script>";
}else{
    $userID = $_user->user_id;
}


try {
    $stmt = $_db->prepare("
    SELECT wish.wish_id, wish.product_id, product.*, product_img.*
    FROM wish
    JOIN product ON wish.product_id = product.product_id
    LEFT JOIN product_img ON product.product_id = product_img.product_id
    WHERE wish.user_id = :user_id
    ");

    $stmt->bindParam(':user_id', $userID);
    $stmt->execute();
    $wishs = $stmt->fetchAll();
    ?>
<main  style="min-height: 600px;">
<section>
<h1 class="wish_title">Wish List</h1>
<div class="wish-box">
<?php
        foreach ($wishs as $wish) {
        $product_img = "../image/" . $wish->product_IMG_source;
    ?>
        <div class="wish-list">
            <div class="product-item" onclick="window.location.href='product_details.php?product_id=<?= $wish->product_id ?>'">
            <input type="hidden" name="productID" value="<?= $wish->product_id ?>">                                 
            <img src="../image/<?= $product_img ?>" alt="<?= $wish->product_name ?>" class="product-image">
            <h3 class="product-name"><?= $wish->product_name ?></h3><br>
            <p class="product-cost">Price: RM<?= number_format($wish->product_price, 2) ?></p><br>                            
            </div>
        </div>
 <?php 
        }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
?>
</div>
</section>
</main>
<?php include "footer.php"; ?>