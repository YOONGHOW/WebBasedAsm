<!DOCTYPE html>
<html lang="en">

<?php
require "../helperFile/Member_base.php";
include "sidebar.php";

// Check if a search term is provided
$searchTerm = $_GET['name'] ?? '';

// Prepare SQL query with filtering
$sql = 'SELECT * FROM users';
$params = [];

if ($searchTerm) {
  $sql .= ' WHERE user_name LIKE :searchTerm';
  $params['searchTerm'] = '%' . $searchTerm . '%';
}

// Prepare statement and execute query
$stmt = $_db->prepare($sql);
$stmt->execute($params);
$arr = $stmt->fetchAll();
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/member_list.css">
  <title>Member Listing</title>

  <style>
    .list-container {
      width: 1200px;
      padding-left: 100px;
    }
  </style>

</head>

<body>
  <div class="list-container">
    <section>
      <h1 id="title-header">Member List</h1>

      <form method="GET">
        <?= searching('name') ?>
        <button type="submit">Search</button>
      </form>
      <br />

      <div class="tbl-header">
        <table cellpadding="0" cellspacing="0">
          <thead style="text-transform:uppercase;">
            <tr>
              <th style="width:5%;">ID</th>
              <th>Name</th>
              <th style="width:15%;">Email</th>
              <th>IC number</th>
              <th>Phone Number</th>
              <th>Gender</th>
              <th>Birth Date</th>
              <th></th>
            </tr>
          </thead>
        </table>
      </div>

      <div class="tbl-content">
        <table>
          <tbody>
            <?php foreach ($arr as $s): ?>
              <tr>
                <td style="width:5%;"><?= $s->user_id ?></td>
                <td><?= $s->user_name ?></td>
                <td  style="width:15%;"><?= $s->Email ?></td>
                <td><?= $s->user_IC ?></td>
                <td><?= $s->user_phoneNumber ?></td>
                <?php
                if ($s->user_gender == "F") {
                  $gender = "Female";
                } else if ($s->user_gender == "M") {
                  $gender = "Male";
                }
                ?>

                <td><?= $gender ?></td>
                <td><?= $s->user_birthday ?></td>
                <td>
                  <a href="member_details.php?id=<?= $s->user_id ?>">Details</a>
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