<?php include "header.php"; ?>
<?php
global $_user;
$_user = $_SESSION['user'] ?? null;

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
<main>
<?php
        foreach ($wishs as $wish) {
        $product_img = "../image/" . $wish->product_IMG_name;
    ?>

<img src="<?= $product_img;?>">

 <?php 
        }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
?>
</main>
<?php include "footer.php"; ?>