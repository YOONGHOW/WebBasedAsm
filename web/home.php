<?php 
include "header.php";

global $_user;

$_user = $_SESSION['user'] ?? null;
?>

<main>
<div class="image-container">
    <img src="../image/homeImage.jpg" alt="Home Image" class="homeImage">
    <div class="black-overlay"></div>
    <div class="content">
        <h1>Welcome to Phaethon Electronic</h1>
        <?php
        if($_user == null){
            echo '<p>Register as a member and purchase it to get more details and discount</p><br>';
            echo '<a href="member_registration.php" class="register-button">Register Now</a>';
        }else{
            echo '<p>Get the promotion and purchase it to get more good electric and bring it to home</p><br>';
            echo '<a href="product_list.php" class="register-button">Buy Now</a>';
        }
        ?>
    </div>
</div>
     
</main>

<?php 
include "footer.php";
?>