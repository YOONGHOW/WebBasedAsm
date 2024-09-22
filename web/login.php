<!DOCTYPE html>
<html>
<?php
require "../helperFile/helper.php";


$_err = [];
global  $password, $email, $cookie_value, $id, $attempt_count;

$attempt_count = intval(@$_COOKIE[$id]);



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
        //check the email is exist or not
        $stm = $_db->prepare('
            SELECT * FROM users
            WHERE email = ?
        ');
        $stm->execute([$email]);
        $u = $stm->fetch();

        if ($u) {
            //retrieve id for checking purpose
            $id = $u->user_id;
            $attempt_count = intval(@$_COOKIE[$id]);
            echo $attempt_count . " with " . $id;

            if ($attempt_count != 3) {
                //then check the password is correct or not;
                $stm = $_db->prepare('SELECT * FROM users WHERE email = ? AND user_password = SHA1(?)');
                $stm->execute([$email, $password]);
                $p = $stm->fetch();
                if ($p) {
                    // if the cookies is exist, remove it tp prevent the validation of next being corrupt
                    if (isset($_COOKIE[$id])) {
                        unset($_COOKIE[$id]);
                        setcookie("$id", '', time() - 3600, '/'); // empty value and old timestamp
                    }

                    //check whether the account is being blocked or not
                    if (strcmp($u->user_freeze, "N") == 0) {
                        temp('info', "Login successfully, welcome $u->user_name");
                        if (strcmp($u->user_rule, "admin") == 0) {
                            //redirect to admin page if the user is an admin
                            login($u, "adminPage.php");
                        } else {
                            //redirect to user page if the user is an member
                            login($u, "../index.php");
                        }
                    } else {
                        echo "<script>alert('Your account has been block. Please contact the management to unblock your account.');</script>";
                    }
                } else if (!$p && $attempt_count < 3) { // if user have wrong password or email for three times, block for 2 minutes

                    setcookie("$id", $attempt_count + 1, time() + 120);
                    temp('info', "You have left " . (3 - $attempt_count) . " chance(s).");
                    $_err['password'] = 'Incorrect password or email';
                }
            } else {
                //if the user already wrong more than three times and being block, display error message
                echo "<script>alert('Your account has been block because of multiple times of error verification. You can try again after a while.');</script>";
            }
        }else{
            $_err['password'] = 'Incorrect password or email';
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
                    <?= generateTextField('email', 'class="login-input" maxlength="100"  placeholder="e.g. xxx@gmail.com" required') ?><br />
                    <?= err('email') ?>
                </div>
                <br>
                <label class="login-label">Password</label><br />
                <div class="input-box">
                    <!-- <input id="password" name="password" type="text" placeholder="Enter your password" required> -->
                    <?= generatePassword('password', 'class="login-input" placeholder="Enter your password" required') ?><br />
                    <?= err('password'); ?>
                    <p>Forget password? <a href="forgetPassword.php">Click here</a></p>
                </div>

                <a href="home.php" class="login-cancel">Cancel</a>
                <input type="submit" class="login-submit" value="LOGIN">

            </form>

        </div>

        <!-- the right part of the login form -->
        <div class="login-component" id="com2">
            <!-- The title and link for register part -->
            <br />
            <h1>Welcome to Phaethon Electronic</h1>
            <p>Didn't register as member yet?</p><br />
            <a href="member_registration.php" class="register-button">Register</a>
        </div>
    </section>
</body>

</html>