<!DOCTYPE html>
<html lang="en">

<?php
// Connect to database
require '../helperFile/helper.php';

//validation part
$_err = [];
global $name, $ic, $birth_date, $gender, $confirm_password, $password, $contact_number, $state, $city;
 $id = generateID("U", "users", "user_id");
if (is_post()) {
   
    $createDate = date("d/m/Y");
    $name = req("name");
    $date = req("date");
    $ic = req("ic");
    $email = req('email');
    $gender = req('gender');
    $contact = req("contact");
    $password = req("password");
    $confirm = req("confirm");
    $address1 = req("address1");
    $address2 = req("address2");
    $postal = req("postal");
    $state = req("state");
    $city = req("city");
    $photo = "login.jpg";
    //req("photo");

    if (checkName($name) !== null) {
        $_err['name'] = checkName($name);
    }

    if(checkIC($ic) !== null){
        $_err['ic'] = checkIC($ic);
    }

    if(checkGmail($email) !== null){
        $_err['email'] = checkGmail($email);
    }

    if(checkPassword($password) !== null){
        $_err['password'] = checkPassword($password);
    }

    if(confirmPassword($password, $confirm) !== null){
        $_err['confirm'] = confirmPassword($password, $confirm);
    }

    if(checkDateFormat($date) !== null){
        $_err['date'] = checkDateFormat($date);
    }
    
    
    // DB operation
    if (empty($_err)) {

        // (1) Save photo$_err['date'] = checkDateFormat($date);
        //$photo = save_photo($f, '../photos');

        // (2) Insert user (member)
        $stm = $_db->prepare('
            INSERT INTO users 
            (
            user_id, Email, user_name, user_IC, user_phoneNumber, user_birthday, 
            user_gender, user_rule, user_password, user_create_date, user_password_update_date,
            user_freeze, admin_position, admin_department, users_IMG_source
            )
            
            VALUES (?, ?, ?, ?, ?, ?, ?, "user", SHA1(?), ?, ?, "N", "none", "none", ?)
        ');
        $stm->execute([$id, $email, $name, $ic, $contact, $date, $gender, $password, $createDate, $createDate, $photo]);

        temp('info', 'You are registered succesfully');
        redirect('login.php');
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/member_registration.css">
    <title>Member Registration</title>
</head>

<body>


    <div class="container">
        <div class="title">Registration</div>
        <div class="content">
            <form action="" method="POST">
                <div class="user-details">

                    <div class="input-box">
                        <label class="details" for="id">Member ID</label>
                        <?= $id ?>
                    </div>
                    <div class="input-box">
                        <label class="details" for="photo">Profile Photo</label>
                        <?= $id ?>
                    </div>
                    <div class="input-box">
                        <label class="details" for="name">Full Name</label>
                        <!-- <input id="name" name="name" type="text" placeholder="Enter your name" required> -->
                        <?= generateTextField('name', 'maxlength="100" placeholder="e.g. Loh Jia Zhe" required') ?>
                        <?= err('name') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="email">Email</label>
                        <?= generateTextField('email', 'maxlength="100"  placeholder="e.g. xxx@gmail.com" required') ?>
                        <?= err('email') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="ic">IC Number</label>
                        <!-- <input id="ic" name="ic" type="text" placeholder="010203020321" required> -->
                        <?= generateTextField('ic', 'maxlength="12" placeholder="e.g. 010203020321" required') ?>
                        <?= err('ic') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="contact">Phone Number</label>
                        <!-- <input id="phone" name="phone" type="text" placeholder="012-1234567" required> -->
                        <?= generateTextField('contact', 'maxlength="11" placeholder="e.g. 0121234567" required') ?>
                        <?= err('contact') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="date">Birthday</label>
                        <!-- <input id="BirthDate" name="BirthDate" type="text" placeholder="1992-04-21" required> -->
                        <?= generateDateField('date') ?>
                        <?= err('date') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="gender">Gender</label>
                        <?= displayGenderList() ?>
                        <?= err('gender') ?>
                    </div>


                    <div class="input-box">
                        <label class="details" for="password">Password</label>
                        <!-- <input id="password" name="password" type="text" placeholder="Enter your password" required> -->
                        <?= generatePassword('password', 'placeholder="Enter your password" required') ?>
                        <?= err('password'); ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="password">Confirm Password</label>
                        <!-- <input id="confirm" name="confirm" type="text" placeholder="Enter your password" required> -->
                        <?= generatePassword('confirm', 'placeholder="Enter again your password" required') ?>
                        <?= err('confirm') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="address1">Address Line 1</label>
                        <!-- <input id="address1" name="address1" type="text" placeholder="Enter your password" required> -->
                        <?= generateTextField('address1') ?>
                        <?= err('address1') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="password">Address Line 2</label>
                        <!-- <input id="address2" name="address2" type="text" placeholder="Enter your add" required> -->
                        <?= generateTextField('address2') ?>
                        <?= err('address2') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="postal">Postal Code</label>
                        <!-- <input id="postal" name="postal" type="text" required> -->
                        <?= generateTextField('postal', 'maxlength="11"') ?>
                        <?= err('postal') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="state">State</label>
                        <?= displayStateList() ?>
                        <?= err('state') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="password">Address Line 2</label>
                        <?= displayCitiesForEachState($state) ?>
                        <?= err('city') ?>
                    </div>


                    <div class="button">
                        <input type="submit" value="Register">
                        <a href="login.php">Cancel</a>
                    </div>

                </div>
            </form>
        </div>
    </div>


</body>

</html>