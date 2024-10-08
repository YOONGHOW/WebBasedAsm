<?php include "header.php"; ?>

<?php 
if($_user == null){
    echo "<script>alert('You must login as member first')
    window.location.href = 'home.php';
    </script>";
}
$stmt = $_db->prepare("
        SELECT * FROM category;");
    $stmt->execute();
    $categorys = $stmt->fetchAll();

?>
 
<main style="min-height: 600px;">
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

<br><br>
    <label>Filter by Price : </label><br><br>
    <input type="submit" name="sort_High_Low" id="priceBtn" value="High to Low"/> <br><br>
    <input type="submit" name="sort_Low_High" id="priceBtn" value="Low to High">


 <select name="category" id="category" onchange="this.form.submit()">
        <option value="all" <?= (isset($_POST['category']) && $_POST['category'] == 'all') ? 'selected' : '' ?>>All</option>
        <?php foreach($categorys as $category){ ?>
        <option value="<?= $category->category_name ?>" <?= (isset($_POST['category']) && $_POST['category'] == $category->category_name) ? 'selected' : '' ?>>
            <?= $category->category_name ?>
        </option>
        <?php } ?>
    </select>

  
  </nav>
  </form>
  <div class="productList">
  <div class="product-list">
<?php
    try {
        
        if(isset($_POST["search"]) || isset($_POST["category"])){
            $searchProd = $_POST["search"] ?? '';
            $selectedCategory = $_POST["category"] ?? 'all';
        
            $sql = "
                SELECT p.*, pi.product_IMG_source, c.*
                FROM product p
                LEFT JOIN product_img pi ON p.product_id = pi.product_id
                LEFT JOIN category c ON p.category_id = c.category_id
                WHERE p.product_name LIKE :searchProd"; 
            
            if ($selectedCategory != 'all') {
                $sql .= " AND c.category_name = :selectedCategory";
            }

            if (isset($_POST['sort_High_Low'])) {
                $sql .= " ORDER BY p.product_price DESC"; 
            }elseif (isset($_POST['sort_Low_High'])) {
                $sql .= " ORDER BY p.product_price ASC"; 
            }
        
            $stmt = $_db->prepare($sql);
            $stmt->bindValue(':searchProd', "%$searchProd%");
            
            if ($selectedCategory != 'all') {
                $stmt->bindValue(':selectedCategory', $selectedCategory);
            }

        }else {
            $stmt = $_db->prepare("
            SELECT p.*, pi.product_IMG_source, c.*
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
            $product_img = "../image/" . $product->product_IMG_source;
    ?>



            <div class="product-item" onclick="window.location.href='product_details.php?product_id=<?= $product->product_id ?>'">
            <input type="hidden" value="<?=$product->category_name?>"/>
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