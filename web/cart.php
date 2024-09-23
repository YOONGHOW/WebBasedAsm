<?php include "header.php"; ?>
<main>


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
    SELECT cart.cart_id, cart.product_id, cart.quantity, product.product_name, product.product_price
    FROM cart
    JOIN product ON cart.product_id = product.product_id
    WHERE cart.user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $userID);
    $stmt->execute();
    $carts = $stmt->fetchAll();
    
        foreach ($carts as $cart) {
    echo '<h1>' . $cart->product_name. '</h1><br>';
    echo '<p>Price: RM' . number_format($cart->product_price, 2) . '</p><br>';
    echo '<p>Quantity: ' . $cart->quantity . '</p><br>';
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }


?>
</main>
<?php include "footer.php"; ?>
