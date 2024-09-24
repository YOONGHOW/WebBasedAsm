<!DOCTYPE html>
<html>
<?php
require "../helperFile/helper.php";

$_err = [];
global  $password, $email;
$_user = $_SESSION["user"] ?? null;

if (is_post()) {
    $oldpassword = trim(req("oldpassword"));
    $password = trim(req("password"));
    $confirm = trim(req("confirm"));
    $updateDate = date("d/m/Y");

    $stm = $_db->prepare('SELECT * FROM users WHERE email = ? AND user_password = SHA1(?)');
    $stm->execute([$_user->Email, $oldpassword]);
    $p = $stm->fetch();

    //Validate: old password
    if ($oldpassword == null) {
        $_err['oldpassword'] = "Please enter your old password.";
    } else if (!$p) {
        $_err['oldpassword'] = "Your old password is incorrect.";
    }

    // Validate: new password
    if (checkPassword($password) != null) {
        $_err['password'] = checkPassword($password);
    }else if(strcmp($oldpassword, $password) == 0){
        $_err['password'] = "Please don't use the previous password.";
    }

    // Validate: confirm password
    if (confirmPassword($password, $confirm) !== null) {
        $_err['confirm'] = confirmPassword($password, $confirm);
    }


    // DB operation
    if (empty($_err)) {

        $stm = $_db->prepare('UPDATE users SET user_password = SHA1(?), user_password_update_date = ? WHERE email = ?');

        $stm->execute([$password, $updateDate, $_user->Email]);
        $u = $stm->fetch();

        temp('info', 'Password reset successfully');
        redirect("member_profile.php");
    }
}
?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link href="../css/jiazhe.css" rel="stylesheet" type="text/css" />

    <style>
        body {
            background-color: #A9A9A9;
        }

        .invalid{
            color:#FF474C;
        }

        .valid{
            color:#90EE90;
        }

        .err{
            color:#FF474C;
        }
    </style>

    <!-- password real time validate -->
    <script>
        window.onload = function() {
            validatePassword();
        };

        function validatePassword() {
            var password = document.getElementById("password").value;
            var xhr = new XMLHttpRequest();

            xhr.open("POST", "validate_password.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the JSON response
                    var response = JSON.parse(xhr.responseText);

                    // Update each criterion based on server response
                    updateCriteria("uppercase", response.uppercase);
                    updateCriteria("lowercase", response.lowercase);
                    updateCriteria("number", response.number);
                    updateCriteria("special", response.special);
                    updateCriteria("length", response.length);
                }
            };

            // Send the password to the server
            xhr.send("password=" + encodeURIComponent(password));
        }

        // Function to update the criteria colour in the UI
        function updateCriteria(elementId, isValid) {
            var element = document.getElementById(elementId);
            if (isValid) {
                element.classList.remove("invalid");
                element.classList.add("valid");
                element.innerHTML = element.innerHTML.replace("❌", "✅");
            } else {
                element.classList.remove("valid");
                element.classList.add("invalid");
                element.innerHTML = element.innerHTML.replace("✅", "❌");
            }
        }
    </script>
</head>

<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>

    <?php if (!$_user) { ?>
        <a href="login.php" id="member-list">Go To Login</a>
        <h1>Please Login first.</h1>
    <?php } else { ?>
        <!-- the entire container for the email form -->
        <section id="login-container" style="margin-top:3em;height:30em;">

            <!-- the title and image -->
            <div class="login-component" id="com1">
                <h2 style="vertical-align: middle;text-align:center;">Change Password</h2><br />
                <img src="../image/change-password.jpeg" style="width:60%;height:65%;margin:auto;display:block;">
            </div>
            <!-- the right part of the login form -->
            <div class="login-component" id="com2" style="padding-top:1em;">

                <form method="POST" action="">
                    <div class="input-box">
                        <label class="details" for="oldpassword" style="float:left;margin-left:10%;">Password</label><br />
                        <!-- <input id="password" name="password" type="text" placeholder="Enter your password" required> -->
                        <?= generatePassword('oldpassword', 'placeholder="Enter your old password"" required') ?><br />
                        <?= err('oldpassword'); ?>

                    </div>
                    <br />
                    <div class="input-box">
                        <label class="details" for="password" style="float:left;margin-left:10%;">New Password</label><br />
                        <!-- <input id="password" name="password" type="text" placeholder="Enter your password" required> -->
                        <?= generatePassword('password', 'placeholder="Enter your new password" oninput="validatePassword()" required') ?><br />
                        <?= err('password'); ?>
                        <div id="validationMessage">
                            <ul style="font-size:13px;width:27em;list-style-type: none;text-align:left;">
                                <li id="uppercase" class="invalid">❌ At least one uppercase letter (A-Z)</li>
                                <li id="lowercase" class="invalid">❌ At least one lowercase letter (a-z)</li>
                                <li id="number" class="invalid">❌ At least one number (0-9)</li>
                                <li id="special" class="invalid">❌ At least one special character (!@#$%^&*)</li>
                                <li id="length" class="invalid">❌ At least 8 characters long</li>
                            </ul>
                        </div>
                    </div>
                    <br />
                    <div class="input-box">
                        <label class="details" for="confirm" style="float:left;margin-left:10%;">Confirm Password</label>
                        <!-- <input id="confirm" name="confirm" type="text" placeholder="Enter your password" required> -->
                        <?= generatePassword('confirm', 'placeholder="Enter again your password" required') ?><br />
                        <?= err('confirm') ?>
                    </div>
                    <br />
                    <input type="submit" class="login-submit" style="float:none;width:12rem;">
                </form>
            </div>
        </section>
    <?php } ?>
</body>

</html>