<?php include "header.php"; ?>
<main>
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
                <input type="number" id="quantity" name="quantity" placeholder="1" min="1" max="<?= $product->product_stock ?>"><br>
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