<!DOCTYPE html>
<html>
<?php
require "../helperFile/helper.php";

$_err = [];
global  $password, $email;
$email = $_SESSION["forgetEmail"] ?? null;

if (is_post()) {
    $password = req("password");
    $confirm = req("confirm");
    $updateDate = date("d/m/Y");


    // Validate: password
    if (checkPassword($password) != null) {
        $_err['password'] = checkPassword($password);
    }

    // Validate: confirm password
    if (confirmPassword($password, $confirm) !== null) {
        $_err['confirm'] = confirmPassword($password, $confirm);
    }


    // DB operation
    if (empty($_err)) {

        $stm = $_db->prepare('UPDATE users SET user_password = SHA1(?), user_password_update_date = ? WHERE email = ?');

        $stm->execute([$password, $updateDate, $email]);
        $u = $stm->fetch();


        temp('info', 'Password reset successfully');
        redirect("login.php");
    }
}
?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="../css/jiazhe.css" rel="stylesheet" type="text/css" />

    <style>
        body {
            background-color: #A9A9A9;
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
    <br />
    <a href="login.php"  id="member-list">Back To Login</a>
    <?php if ($email == null) { ?>
        <h1>Please validate your email first.</h1>
    <?php } else { ?>
        <!-- the entire container for the email form -->
        <section id="login-container">

            <!-- the title and image -->
            <div class="login-component" id="com1">
                <h2 style="vertical-align: middle;text-align:center;">Reset Password</h2><br />
                <img src="../image/reset-password.jpeg" style="width:50%;height:65%;margin:auto;display:block;">
            </div>
            <!-- the right part of the login form -->
            <div class="login-component" id="com2" style="padding-top:1em;">
                <!-- if the email is not yet entered or have error, display the form; otherwise display the varification -->
                <!-- The email form part -->
                <h3>Please enter your new password</h3>

                <form method="POST" action="">
                    <div class="input-box">
                        <label class="details" for="password" style="float:left;margin-left:10%;">Password</label><br />
                        <!-- <input id="password" name="password" type="text" placeholder="Enter your password" required> -->
                        <?= generatePassword('password', 'placeholder="Enter your password"  oninput="validatePassword()" required') ?><br />
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