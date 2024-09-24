<!DOCTYPE html>
<html lang="en">

<?php
include "sidebar.php";
require '../helperFile/ProductMaintenance_base.php';

$search = isset($_POST['search']) ? $_POST['search'] : '';

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

    .addButton {
      margin-top: 10px;
      float: right;
    }

    .addButton #button {
      border-radius: 20px;
      border: none;
      background-color: #6495ED;
      font-weight: bold;
      height: 40px;
      width: 160px;
      cursor: pointer;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
      transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
    }

    .addButton #button:hover {
      background-color: #4169E1;
      box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.3);
      transform: translateY(-2px);
      opacity: 0.9;
    }

    .addButton #button:active {
      background-color: #1E3A8A;
      box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
      transform: translateY(1px);
    }

    tr.low-stock {
      background-color: #ffcccc !important;
    }
  </style>
</head>

<body>
  <section>
    <div class="header-container">
      <form action="" method="post" class="search-form">
        <div class='searching'>
          <?= html_text('search', 'maxlength="100" placeholder="Enter the product name to search"') ?>
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
              <th>Product Photo</th>
              <th>Category ID</th>
              <th>Product Name</th>
              <th>Product Price (RM)</th>
              <th>Stock</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
        </table>
      </div>

      <div class="tbl-content">
        <table cellpadding="0" cellspacing="0" border="0">
          <tbody id="product-table-body">
            <?php foreach ($arr as $s): ?>

              <!-- if stock less than 10 low-stock class will be execute -->
              <tr class="<?= ($s->product_stock < 10) ? 'low-stock' : 'regular-stock' ?>">
                <td><?= $s->product_id ?></td>
                <td>
                  <a href="productAdmin_image.php?id=<?= $s->product_id ?>"><img src="<?= $s->product_IMG_source ?>" alt="photo" class="product-img">
                    <br /><span id='photo-link'>Click to change</span></a>
                </td>
                <td><?= $s->category_id ?></td>
                <td><?= $s->product_name ?></td>
                <td><?= $s->product_price ?></td>
                <td><?= $s->product_stock ?></td>
                <td><?= $s->product_status ?></td>
                <td>
                  <div class="action-links">
                    <a href='productAdmin_details.php?id=<?= $s->product_id ?>'>Details</a>
                    <a href="productAdmin_update.php?id=<?= $s->product_id ?>">Update</a>
                    <a href="productAdmin_delete.php?id=<?= $s->product_id ?>">Delete</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="addButton">
      <button id="button" onclick="window.location.href='productAdmin_add.php'">ADD Product +</button>
    </div>

    <!-- Pass product data to JS BEFORE including external JS file -->
    <script>
      const products = <?= json_encode($arr); ?>;
    </script>

    <script src="../js/low-stock.js"></script>
  </section>
</body>

</html>