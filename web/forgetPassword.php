<!DOCTYPE html>
<html>
<?php
require "../helperFile/helper.php";
$_err = [];
global $email, $validCheck, $newOtp;

$validCheck = false;

if (is_post()) {
    $email = req('email');

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Please enter the email';
    } else if (!preg_match("/^[A-Za-z0-9]+@[A-Za-z0-9\.]+$/", $email)) {
        $_err['email'] = 'Invalid email format';
    } else if (!memberGmailExist($email)) {
        $_err['email'] = 'The email is not exist.';
    }

    //DB operation
    if (!$_err) {
        //store the email in the session
        $_SESSION['forgetEmail'] = $email;

        $newOtp = rand(100000, 999999);
        //display the page that let user enter otp code
        $validCheck = true;
        // Send email

        $m = get_mail();
        $m->addAddress("jiazhehello@gmail.com");
        $m->Subject = "Email Verification";
        $m->Body = "$newOtp is your email verification code, $email";
        $m->send();

        temp('info', 'A verification email is send. Please check your email.');
    }
} else if (is_get()) {
    $newOtp = req("newOtp");
    $otp = req("otp");

    if ($otp == null) {
        $_err['otp'] = "Please enter the otp code.";
    } else if ($otp != $newOtp) {
        $validCheck = true;
        $_err['otp'] = "invalid otp entered. $newOtp";
    }

    if (!$_err) {

        temp('info', 'Verification successfully');
        redirect("changePassword.php");
    }
}
?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Forget Password</title>
    <link href="../css/jiazhe.css" rel="stylesheet" type="text/css" />

    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<body>
    <a href="login.php">back to login</a>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>
    <!-- the entire container for the email form -->
    <section id="login-container">

        <!-- the title and image -->
        <div class="login-component" id="com1">
            <h2 style="vertical-align: middle;text-align:center;">Forget Password</h2><br />
            <img src="../image/forgetpassword.avif" style="width:50%;height:65%;margin:auto;display:block;">


        </div>
        <!-- the right part of the login form -->
        <div class="login-component" id="com2">
            <!-- if the email is not yet entered or have error, display the form; otherwise display the varification -->
            <?php if (!$validCheck) { ?>
                <!-- The email form part -->
                <p>Please enter your email address</p>

                <form method="POST" action="">
                    <div class="input-box">
                        <?= generateTextField('email', 'class="login-input" maxlength="100"  placeholder="e.g. xxx@gmail.com" required') ?>
                        <br /><?= err('email') ?>
                    </div>
                    <br />
                    <input type="submit" class="login-submit" style="float:none;width:12rem;">
                </form>

        </div>
    <?php } else { ?>
        <!-- The email form part -->
        <p>Please get the OTP from your email and enter code</p>

        <form method="GET" action="">
            <div class="input-box">
                <?= generateNumberField('otp', 'class="login-input" min=100000 max=999999  placeholder="e.g. 123456" required') ?>
                <input type="hidden" value="<?= $newOtp ?>" name="newOtp">
                <br /><?= err('otp') ?>
            </div>
            <br />
            <input type="submit" class="login-submit" style="float:none;width:12rem;">
        </form>
    <?php } ?>
    </section>
</body>

</html>