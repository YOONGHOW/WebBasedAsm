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
<section class="cart_section">
<nav class="cart_side">
    <ul>


    <?php
        $total_price = 0;
        $ship_fee = 4.9;
        $discount = 0;

        foreach ($carts as $cart) {
        $product_img = "../image/" . $cart->product_IMG_name;
    ?>

    <div class="cart_container">
        <div class="cart_image_box" >
            <img src="../image/<?=$product_img ?>" alt="<?= $cart->product_name ?>">
        </div>
        <div class="cart_details_box">
            <h1><?= $cart->product_name ?></h1><br>
            <p>Price: RM<?= number_format($cart->product_price, 2) ?></p><br>
            <p>Quantity: <?= $cart->quantity ?></p><br>
            <p>Total: RM<?= number_format($cart->product_price * $cart->quantity, 2) ?></p><br>
        </div>
    </div>

    <?php

    $total_price +=  $cart->product_price * $cart->quantity;
    $total_payment = $total_price + $ship_fee + $discount;
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
?>
      </ul>
      </nav>

   <div class="cart_total_container">

        <div class="payment-details">
        <p class="label">Total:</p>
        <p>RM <?= number_format($total_price, 2) ?></p>
    </div>

    <div class="payment-details">
        <p class="label">Shipping Fee:</p>
        <p>RM <?= number_format($ship_fee, 2) ?></p>
    </div>

    <div class="payment-details">
        <p class="label">Voucher Discount:</p>
        <p>RM <?= number_format($discount, 2) ?></p>
    </div>
<br>
    <div class="payment-details">
        <p class="label">Total Payment:</p>
        <p>RM <?= number_format($total_payment, 2) ?></p>
    </div>

    <input type="submit" name="checkOutBtn" id="checkOutBtn" value="Place Order"/>
    </div>


</div>
</section>
<br>



</main>
<?php include "footer.php"; ?>
