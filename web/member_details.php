<!DOCTYPE html>
<html lang="en">

<?php
// Connect to the database
require '../helperFile/helper.php';

global $user, $block, $completeAddress;


// Get the user ID from the URL
$userId = $_GET['id'] ?? null;

if ($userId) {
    // Retrieve user details
    $sql = 'SELECT * FROM users WHERE user_id = ?';
    $stmt = $_db->prepare($sql);
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (strcmp($user->user_freeze, "N") == 0) {
        $block = "unblock";
    } else {
        $block = "block";
    }

    $sql = 'SELECT * FROM address WHERE user_id = ?';
    $stmt = $_db->prepare($sql);
    $stmt->execute([$userId]);
    $address = $stmt->fetch();

    if($address){
        $completeAddress = $address->complete_address .", ". $address->zipCode ." ".  $address->state .", ".  $address->city;
    }else{
        $completeAddress = "none";
    }
}

if (is_post()) {
    $block = req("block") ?? "";

    $freeze = (strcmp($block, "block") == 0) ? "Y" : "N";

    $sql = 'UPDATE users SET user_freeze = ? WHERE user_id = ?';
    $stmt = $_db->prepare($sql);
    $stmt->execute([$freeze, $userId]);
}


?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/jiazhe.css">
    <title>Update Member Information</title>

    <style>
        body {
            background-color: #A9A9A9;
        }
    </style>
</head>

<body>


    <div class="login-container" id="com2" style="width:50em;margin:auto;padding-top:1em;">
        <a href="member_list.php" id="back-hyperlink">
            <h2>Back To List</h2>
        </a>
        <form method="POST" action="">
            <?php
            if (strcmp($user->user_rule, "admin") != 0) {
                if (strcmp($block, "unblock") == 0) {
                    echo '<input type="hidden" name="block" value="block">';
                    echo '<button id="block" >Block The Account</button>';
                } else {
                    echo '<input type="hidden" name="block"  value="unblock">';
                    echo '<button id="block">Unblock The Account</button>';
                }
            }
            ?>


        </form></br />
        <img src="../image/<?= $user->users_IMG_source ?>" id="image-content">
        <br />
        <h1><?= $user->user_name ?> </h1>
        <table id="detail-table">
            <tr>
                <td>
                    <h3>Email</h3>
                    <p><?= $user->Email ?></p>
                </td>
                <td>
                    <h3>Phone Number</h3>
                    <p><?= $user->user_phoneNumber ?></p>
                </td>
                <td>
                    <h3>Gender</h3>
                    <p><?= (strcmp($user->user_gender, "F") ? "Female" : "Male") ?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <h3>Birth Date</h3>
                    <p><?= $user->user_birthday ?></p>
                </td>
                <td>
                    <h3>Account Create Date</h3>
                    <p><?= $user->user_create_date ?></p>
                </td>
                <td>
                    <h3>Address</h3>
                    <p><?= $completeAddress ?></p>
                </td>

            </tr>
        </table>
    </div>



</body>

</html>