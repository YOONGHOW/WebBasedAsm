<!DOCTYPE html>
<html lang="en">

<?php
require '../ProductMaintenance_base.php';

//sql select all query
$sql = 'SELECT * FROM product';
$params = [];

// Prepare statement and execute query
$stmt = $_db->prepare($sql);
$stmt->execute($params);
$arr = $stmt->fetchAll();

?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/product_list.css">
  <title>Product Listing</title>
</head>

<body>
  <a href="../index.php">Back</a>
  <section>
    <h1>Product List</h1>

    <div class="tbl-header">
      <table cellpadding="0" cellspacing="0" border="0">
        <thead style="text-transform:uppercase;">
          <tr>
            <th>Product ID</th>
            <th>Category ID</th>
            <th>Product Name</th>
            <th>Product Price</th>
            <th> Stock</th>
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
              <td><?= $s->category_id ?></td>
              <td><?= $s->product_name ?></td>
              <td><?= $s->product_price ?></td>
              <td><?= $s->product_stock ?></td>
              <td><?= $s->product_create_date ?></td>
              <td><?= $s->product_status ?></td>
              <td>
                <span style="margin-right: 10px;"><a href="productAdmin_update.php?id=<?=$s->product_id ?>">Update</a>
                </span><a>Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

    </div>
  </section>

</body>

</html>