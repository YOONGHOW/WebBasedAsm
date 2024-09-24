
<?php include "header.php"; ?>

<main>
    <p class="introduction">A comprehensive range from electronic devices
         to home appliances are available at here !</p>
<section>
  <nav class="prod_side">
    <form method="POST" action="product_list.php">
    <input type="search" name="search" id="search_prod" placeholder="Search..." 
    <?php if(isset($_POST['search'])){
    echo 'value="'.$_POST['search'].'"';    
}
    
    ?>/>
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
        if(isset($_POST["search"])){
            $searchProd = $_POST["search"];
            $stmt = $_db->prepare("
            SELECT p.*, pi.product_IMG_name, c.category_name
            FROM product p
            LEFT JOIN product_img pi ON p.product_id = pi.product_id
            LEFT JOIN category c ON p.category_id = c.category_id
            WHERE p.product_name LIKE '%$searchProd%'

        ");           
        }else{
        $stmt = $_db->prepare("
        SELECT p.*, pi.product_IMG_name, c.category_name
        FROM product p
        LEFT JOIN product_img pi ON p.product_id = pi.product_id
        LEFT JOIN category c ON p.category_id = c.category_id
        ");
        }
    $stmt->execute();
    $products = $stmt->fetchAll();
    if($products == null){
    echo '<p>No record found(s)</p>';
    }

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
</form>
</main>

<?php include "footer.php"; ?>