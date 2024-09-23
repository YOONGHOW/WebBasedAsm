<?php include "header.php"; ?>
<?php 
global $_user;
$_user = $_SESSION['user'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addCart'])) {
    $userID = $_user->user_id;  
    $productID = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    $checkSql = "SELECT quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id";
    $checkStmt = $_db->prepare($checkSql);
    $checkStmt->bindParam(':user_id', $userID);
    $checkStmt->bindParam(':product_id', $productID);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        $existingQuantity = $checkStmt->fetchColumn();
        $newQuantity = $existingQuantity + $quantity;

        $updateSql = "UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";
        $updateStmt = $_db->prepare($updateSql);
        $updateStmt->bindParam(':quantity', $newQuantity);
        $updateStmt->bindParam(':user_id', $userID);
        $updateStmt->bindParam(':product_id', $productID);
        
        if ($updateStmt->execute()) {
            echo '<script>
            alert("Quantity updated in cart");
            window.location.href = "product_list.php";        
            </script>';
        } else {
            echo "<script>alert('Error: Could not update quantity in cart.')</script>";
        }

    } else {
        $newCartID = generateID('CART', 'cart', 'cart_id');
        
        $sql = "INSERT INTO cart (cart_id, user_id, product_id, quantity)
                VALUES (:cart_id, :user_id, :product_id, :quantity)";
        
        $stmt = $_db->prepare($sql);
        $stmt->bindParam(':cart_id', $newCartID);
        $stmt->bindParam(':user_id', $userID);
        $stmt->bindParam(':product_id', $productID);
        $stmt->bindParam(':quantity', $quantity);
        
        if ($stmt->execute()) {
            echo '<script>
            alert("Item added to cart");
            window.location.href = "product_list.php";        
            </script>';
        } else {
            echo "<script>alert('Error: Could not add item to cart.')</script>";
        }
    }
}

?>


<main style="min-height: 500px;">
<?php
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $stmt = $_db->prepare("SELECT p.*, pi.product_IMG_name 
                           FROM product p 
                           LEFT JOIN product_img pi ON p.product_id = pi.product_id 
                           WHERE p.product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR); 
    $stmt->execute();

    $product = $stmt->fetch(PDO::FETCH_OBJ);

    if ($product) {
?>
        <nav class="image_side">

        <?php
        echo '<img src="../image/' . $product->product_IMG_name . '" alt="' . $product->product_name . '">';
        ?>
        </nav>
        <div class="details_side">
                <?php
                 echo '<h1>'. $product->product_name .'</h1><br>';
                 echo '<p style="font-size:22px;"><b>RM' . number_format($product->product_price, 2) . 
                 ' | <span style="font-size:17px;">Stock: ' .$product->product_stock .'</span>
                 </p></b><br>';
                 echo '<article>' . $product->product_description .'</article>';
                ?>

                <form method="POST" action="product_details.php">
                <br>
                <label for="quantity" class="qty">Quantity :</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $product->product_stock ?>"><br>
                <input type="hidden" name="product_id" value="<?= $product->product_id ?>">
                <input type="hidden" name="product_name" value="<?= $product->product_name ?>">
                <input type="hidden" name="product_price" value="<?= $product->product_price ?>">
                <input type="submit" id="addCartBtn" name="addCart" value="Add to Cart"/><br>
                <input type="button" id="wishBtn" name="wishList" value="Save To wishlish"/>
                </form>
                
        </div>  

<?php
    } else {
        echo 'Product not found!';
    }
} else {
    echo 'No product selected!';
}
?>
<section>
</main>
<?php include "footer.php"; ?>