
<?php include "header.php"; ?>

<main>
    <p style="text-align:center; background-color:grey; font-size:18px;">Find Your Product At Here</p>
<section>
  <nav class="prod_side">
    <select name="category" id="category">
  <option value="category">All</option>
  <option value="study">Study Room</option>
  <option value="bedroom">Bedroom</option>
  <option value="kitchen">Kitchen</option>
  <option value="bathroom">Bathroom</option>
  <option value="games">Games Room</option>
  <option value="living">Living Room</option>
  <option value="dining">Dining Room</option>
  <option value="garage">Garage</option>
</select>
  </nav>
  
  <div class="productList">

    <div class="product-list">

<?php
    try {
    $stmt = $_db->prepare("
        SELECT p.*, pi.product_IMG_name
            FROM product p
            LEFT JOIN product_img pi ON p.product_id = pi.product_id
    ");
    $stmt->execute();
    $products = $stmt->fetchAll();
    
        foreach ($products as $product) {
            $product_img = "../image/" . $product->product_IMG_name;
    ?>
            <div class="product-item" onclick="window.location.href='product_details.php?product_id=<?= $product->product_id ?>'">         
            <input type="hidden" value="<?=$product->product_id?>"/>
            <img src="<?=$product_img ?>" alt="<?=$product->product_name ?>'" class="product-image">
            <h3 class="product-name"><?=$product->product_name ?></h3>
            <p class="product-cost">RM<?=$product->product_price?></p>
            </div>

    <?php       
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
    ?>

</div>

</div>
</section>
</main>

<?php include "footer.php"; ?>