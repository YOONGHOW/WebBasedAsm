<!DOCTYPE html>
<html lang="en">

<?php
include "AdminHeader.php";
require '../helperFile/ProductMaintenance_base.php';

// Get search information and price range from the user
$search = isset($_POST['search']) ? $_POST['search'] : '';


// SQL query to get only the first image for each product
$sql = 'SELECT p.*, (SELECT pi.product_IMG_source FROM product_img pi WHERE pi.product_id = p.product_id LIMIT 1) as product_IMG_source 
        FROM product p WHERE 1=1';

if ($search) {
  $sql .= ' AND p.product_name LIKE :search';
}

$stmt = $_db->prepare($sql);

if ($search) {
  $stmt->bindValue(':search', '%' . trim($search) . '%');
}

$stmt->execute();
$arr = $stmt->fetchAll();
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/productAdmin_list.css">
  <title>Product Listing</title>

  <style>
    .product-img {
      width: 60px;
      height: 60px;
      border-radius: 100%;
    }

  </style>
</head>

<body>
  <a href="adminPage.php">Back</a>
  <section>
  <div class="header-container">
    <form action="" method="post" class="search-form">
      <div class='searching'>
        <?=html_text('search','maxlength="100" placeholder="Enter the product name to search"')?>
        <button><img src="../image/search_icon.png"></button>
      </div>
    </form>
    <h1>Product List</h1>
  </div>

<div class="list-container">
    <div class="tbl-header">
      <table cellpadding="0" cellspacing="0" border="0">
        <thead style="text-transform:uppercase;">
          <tr>
            <th>Product ID</th>
            <th>product photo</th>
            <th>Category ID</th>
            <th>Product Name</th>
            <th>Product Price</th>
            <th>Stock</th>
            <th>Create Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
      </table>
    </div>

    <div class="tbl-content">
      <table cellpadding="0" cellspacing="0" border="0">
        <tbody>
          <?php foreach ($arr as $s): ?>
            <tr>
              <td><?= $s->product_id ?></td>
              <td>
                <a href="productAdmin_image.php?id=<?= $s->product_id ?>"><img src="<?= $s->product_IMG_source ?>" alt="photo" class="product-img">
                  <br />Click to change</a>
              </td>
              <td><?= $s->category_id ?></td>
              <td><?= $s->product_name ?></td>
              <td><?= $s->product_price ?></td>
              <td><?= $s->product_stock ?></td>
              <td><?= $s->product_create_date ?></td>
              <td><?= $s->product_status ?></td>
              <td>
                <span style="margin-right: 10px;"><a href="productAdmin_update.php?id=<?= $s->product_id ?>">Update</a>
                </span><a href="productAdmin_delete.php?id=<?= $s->product_id ?>">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
</div>
  </section>

</body>

</html>