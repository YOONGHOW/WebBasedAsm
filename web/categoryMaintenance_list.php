<!DOCTYPE html>
<html lang="en">

<?php
include "sidebar.php";
require '../helperFile/ProductMaintenance_base.php';

// Get search information from the user
$search = isset($_POST['search']) ? $_POST['search'] : '';

// fetch all category data 
$sql = "SELECT * FROM CATEGORY WHERE 1=1";
if ($search) {
    $sql .= ' AND category_name LIKE :search';
}

$stmt = $_db->prepare($sql);

if ($search) {
    $stmt->bindValue(':search', '%' . trim($search) . '%');
}

$stmt->execute();
$categories = $stmt->fetchAll();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/productAdmin_list.css">
    <link rel="stylesheet" href="../css/categoryMaintenance_list.css">
    <title>Category Listing</title>

    <style>
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
    </style>
</head>

<body>
    <section>
        <div class="header-container">
            <form action="" method="post" class="search-form">
                <div class='searching'>
                    <?= html_text('search', 'maxlength="100" placeholder="Enter the category name to search" value="' . htmlspecialchars($search) . '"') ?>
                    <button><img src="../image/search_icon.png"></button>
                </div>
            </form>
            <h1>Category List</h1>
        </div>

        <div class="list-container">
            <div class="tbl-header">
                <table cellpadding="0" cellspacing="0" border="0">
                    <thead style="text-transform:uppercase;">
                        <tr>
                            <th>Category ID</th>
                            <th>Category Name</th>
                            <th>Category Description</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="tbl-content">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                        <?php foreach ($categories as $s): ?>
                            <tr>
                                <td><?= $s->category_id ?></td>
                                <td><?= $s->category_name ?></td>
                                <td><?= $s->category_description ?></td>
                                <td>
                                    <div class="action-links">
                                        <a href="categoryMaintenance_update.php?id=<?= $s->category_id ?>">Update</a>
                                        <a href="categoryMaintenance_delete.php?id=<?= $s->category_id ?>">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="addButton">
            <button id="button" onclick="window.location.href='categoryMaintenance_add.php'">ADD Category +</button>
        </div>
    </section>
</body>

</html>
