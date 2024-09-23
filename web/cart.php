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
    SELECT cart.cart_id, cart.product_id, cart.quantity, product.*, product_img.*
    FROM cart
    JOIN product ON cart.product_id = product.product_id
    LEFT JOIN product_img ON product.product_id = product_img.product_id
    WHERE cart.user_id = :user_id
    ");

    $stmt->bindParam(':user_id', $userID);
    $stmt->execute();
    $carts = $stmt->fetchAll();
    ?>


<main style="min-height: 500px;">

<span style="display: flex; align-items: center; margin-left:670px; padding:15px;">
  <img src="../image/shopping-cart.png" alt="cart" style="margin-right: 10px; width:30px; height:30px;">
  <h1 style="margin: 0;">My Cart</h1>
</span><br>

    <?php
        $total_price = 0;
        foreach ($carts as $cart) {
        $product_img = "../image/" . $cart->product_IMG_name;
    ?>
    <section>
    
    <div class="cart_container">
        <div class="cart_image_box">
            <img src="../image/<?=$product_img ?>" alt="<?= $cart->product_name ?>">
        </div>
        <div class="cart_details_box">
            <h1><?= $cart->product_name ?></h1>
            <p>Price: RM<?= number_format($cart->product_price, 2) ?></p>
            <p>Quantity: <?= $cart->quantity ?></p>
            <p>Total: RM<?= number_format($cart->product_price * $cart->quantity, 2) ?></p>
        </div>
    </div>


    <?php

    $total_price +=  $cart->product_price;
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
?>

<div class="cart_total">
    <p>Total :RM <?= $total_price ?></p>
</div>
    </section><br>




</main>
<?php include "footer.php"; ?>
