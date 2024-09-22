<!DOCTYPE html>
<html>
<?php
require "../helperFile/helper.php";

$_err = [];
global  $password, $email;

if (is_post()) {


    $email = req('email');
    $password = req("password");

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Please enter the email';
    } else if (!preg_match("/^[A-Za-z0-9]+@[A-Za-z0-9\.]+$/", $email)) {
        $_err['email'] = 'Invalid email format';
    }

    // Validate: password
    if ($password == '') {
        $_err['password'] = 'Please enter your password.';
    }


    // Login user
    if (!$_err) {
        // TODO
        $stm = $_db->prepare('
            SELECT * FROM users
            WHERE email = ? AND user_password = SHA1(?)
        ');
        $stm->execute([$email, $password]);
        $u = $stm->fetch();

        if ($u) {
            temp('info', "Login successfully, welcome $u->user_name");
            if(strcmp($u->user_rule, "admin")==0){
                login($u, "adminPage.php");
            }else{
                login($u, "../index.php");
            }
            
        } else {
            $_err['password'] = 'Not matched';
        }
    }
}
?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="../css/jiazhe.css" rel="stylesheet" type="text/css" />


</head>
<style>
    html {
        background-image: url('../image/login2.jpg');
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: 100% 100%;
    }
</style>

<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>

    <!-- the entire container for the login form -->
    <section id="login-container">

        <!-- the left part of the login form -->
        <div class="login-component" id="com1">
            <h2>Sign In</h2><br />

            <!-- the data form for user to input their email and password for login purpose -->
            <form method="POST" action="">
                <div class="input-box">
                    <label class="details" for="email">Email</label><br />
                    <?= generateTextField('email', 'class="login-input" maxlength="100"  placeholder="e.g. xxx@gmail.com" required') ?>
                    <?= err('email') ?>
                </div>
                <br>
                <label class="login-label">Password</label><br />
                <div class="input-box">
                    <!-- <input id="password" name="password" type="text" placeholder="Enter your password" required> -->
                    <?= generatePassword('password', 'class="login-input" placeholder="Enter your password" required') ?>
                    <?= err('password'); ?>
                    <p>Forget password? <a href="forgetPassword.php">Click here</a></p>
                </div>

                <input type="submit" class="login-submit" value="LOGIN">
            </form>

        </div>

        <!-- the right part of the login form -->
        <div class="login-component" id="com2">
            <!-- The title and link for register part -->
            <h1>Welcome to Resort World</h1>
            <p>Didn't register as member yet?</p><br />
            <a href="member_registration.php" class="register-button">Register</a>
        </div>
    </section>
</body>

</html>