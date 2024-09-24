<?php include "header.php"; ?>

<?php 

$stmt = $_db->prepare("
        SELECT * FROM category;");
    $stmt->execute();
    $categorys = $stmt->fetchAll();

?>
 
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
 <select name="category" id="category" onchange="this.form.submit()">
        <option value="all" <?= (isset($_POST['category']) && $_POST['category'] == 'all') ? 'selected' : '' ?>>All</option>
        <?php foreach($categorys as $category){ ?>
        <option value="<?= $category->category_name ?>" <?= (isset($_POST['category']) && $_POST['category'] == $category->category_name) ? 'selected' : '' ?>>
            <?= $category->category_name ?>
        </option>
        <?php } ?>
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