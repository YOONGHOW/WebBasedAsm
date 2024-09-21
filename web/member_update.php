<!DOCTYPE html>
<html lang="en">

<?php
// Connect to the database
require '../helperFile/helper.php';

// Get the user ID from the URL
$userId = $_GET['id'] ?? null;

$_err = [];

if ($userId) {
    // Retrieve user details
    $sql = 'SELECT * FROM users WHERE user_id = :userId';
    $stmt = $_db->prepare($sql);
    $stmt->execute(['userId' => $userId]);
    $user = $stmt->fetch();

    // Set the global variables for each field
    $GLOBALS['name'] = $user->user_name;
    $GLOBALS['phone'] = $user->user_phoneNumber;
    $GLOBALS['BirthDate'] = $user->user_birthday;
    $GLOBALS['password'] = $user->user_password;
}

// Check if the form is submitted for updating user details
if (is_post()) {
    // Retrieve updated details 
    $name = req('name');
    $ic = req('ic');
    $phone = req('phone');
    $BirthDate = req('BirthDate');
    $user_id = req('user_id');

    if (checkName($name) !== null) {
        $_err['name'] = checkName($name);
    }

    if (checkDateFormat($BirthDate) !== null) {
        $_err['BirthDate'] = checkDateFormat($BirthDate);
    }

    if (!$_err) {
        // Prepare the update query
        $stm = $_db->prepare("UPDATE users SET user_name = ?, user_phoneNumber = ?, user_birthday = ? WHERE user_id = ?");

        $stm->execute([$name, $phone, $BirthDate, $user_id]);
        $u = $stm->fetch();
        // check the query result
        if ($u) {
            temp('info', 'User info update successfully');
            redirect("member_list.php");
        } else {
            echo "Failed to update user details.";
        }
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/member_registration.css">
    <title>Update Member Information</title>
</head>

<body>
    <div class="container">
        <a href="member_list.php">Back</a>

        <div class="title">Update Member Information</div>
        <div class="content">
            <form method="POST" action="">
                <input type="hidden" name="user_id" value="<?= $userId ?>">

                <div class="user-details">
                    <div class="input-box">
                        <label class="details" for="name">Full Name</label>
                        <?= generateTextField('name', 'maxlength="100" required') ?>
                        <?= err('name') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="phone">Phone Number</label>
                        <?= generateTextField('phone', 'maxlength="15" required') ?>
                        <?= err('phone') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="BirthDate">Birthday</label>
                        <?= generateDateField('BirthDate', 'required') ?>
                        <?= err('BirthDate') ?>
                    </div>


                    <div class="button">
                        <input type="submit" name="submit" value="Update">
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>