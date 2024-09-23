<!DOCTYPE html>
<?php
// Connect to database
require '../helperFile/helper.php';
global $_user;

$_user = $_SESSION['user'] ?? null;
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../image/bigLogo.png">
    <title>Phaethon ELECTRONIC</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/product_list.css">
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
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="aboutUs.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if ($_user == null) { ?>
                        <li><a href="login.php">Login</a></li>
                    <?php } else { ?>
                        <?php if ($_user): ?>
                            <li><a href="profile.php">Profile</a></li>
                        <?php endif ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </header>