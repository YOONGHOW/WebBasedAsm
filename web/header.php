<!DOCTYPE html>
<?php
// Connect to database
require '../helperFile/helper.php';
global $_user;

$_user = $_SESSION['user'] ?? null;

$total_item = 0;
if ($_user) {
    try {
        $userID = $_user->user_id;
        $stmt = $_db->prepare("SELECT quantity FROM cart WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userID);
        $stmt->execute();
        $carts = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach ($carts as $cart) {
            $total_item += $cart->quantity;
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }



}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../image/bigLogo.png">
    <title>Phaethon ELECTRONIC</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/product_list.css">
    <script src="cart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>
    <header>
        <div class="header-content">
            <div class="logo-title">
                <img src="../image/logo.png" alt="logo">
                <p style="color:white">Phaethon Electronic</p>
            </div>
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="product_list.php">Products</a></li>
                    <li><a href="promotion_list.php">Promotion</a></li>
                    <li><a href="wish_list.php">Wish
                    <span style="background-color:red; border:none; border-radius:50%; padding:4px;font-size:10px;"><?= $total_item ?></span>
                    </a></li>
                    <li><a href="cart.php">Cart 
                        <span style="background-color:red; border:none; border-radius:50%; padding:4px;font-size:10px;"><?= $total_item ?></span>
                    </a></li>
                    <?php if ($_user == null) { ?>
                        <li><a href="login.php">Login</a></li>
                    <?php } else { ?>
                        <?php if ($_user): ?>
                            <li><a href="member_profile.php">Profile</a></li>
                        <?php endif ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </header>