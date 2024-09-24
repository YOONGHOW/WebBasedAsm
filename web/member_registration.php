<!DOCTYPE html>
<html lang="en">

<?php
// Connect to database
require '../helperFile/helper.php';

//validation part
$_err = [];
global $name, $ic, $date, $gender, $confirm, $password, $contact, $state, $city, $address1, $address2, $postal, $photo, $validate;
$id = generateID("U", "users", "user_id");
$addressID = generateID("A", "address", "address_id");
$state = req("state") ?? "none";
$validate = false;
$_SESSION['verify'] = $validate;

if (is_post()) {
    $validate = $_SESSION['verify'];
    $photo = get_file('photo');
    $createDate = date("d/m/Y");
    $name = trim(req("name"));
    $date = req("date");
    $ic = req("ic");
    $email = trim(req('email'));
    $gender = req('gender');
    $contact = trim(req("contact"));
    $password = trim(req("password"));
    $confirm = trim(req("confirm"));
    $address1 = trim(req("address1"));
    $address2 = trim(req("address2"));
    $postal = req("postal");
    $city = req("city");

    if (checkImage($photo)) {
        $_err['photo'] = checkImage($photo);
    }

    if (checkName($name) !== null) {
        $_err['name'] = checkName($name);
    }

    if (checkIC($ic) !== null) {
        $_err['ic'] = checkIC($ic);
    }

    if (checkGmail($email) !== null) {
        $_err['email'] = checkGmail($email);
    }

    if (checkPassword($password) !== null) {
        $_err['password'] = checkPassword($password);
    }

    if (confirmPassword($password, $confirm) !== null) {
        $_err['confirm'] = confirmPassword($password, $confirm);
    }

    if (checkDateFormat($date) !== null) {
        $_err['date'] = checkDateFormat($date);
    }

    if (strcmp($gender, "none") == 0) {
        $_err['gender'] = "Please select your gender.";
    }

    if (strcmp($state, "none") == 0) {
        $_err['state'] = "Please select your state.";
    }

    if (strcmp($city, "none") == 0) {
        $_err['city'] = "Please select your city.";
    }

    if (checkAddress($address1) != null) {
        $_err['address1'] = checkAddress($address1);
    }

    if (checkAddress($address2) != null) {
        $_err['address2'] = checkAddress($address2);
    }

    if (checkPostal($postal) != null) {
        $_err['postal'] = checkPostal($postal);
    }


    // DB operation
    if (empty($_err)) {
        $completeAddress = $address1 . ", " . $address2;
        $stateName = convertState($state);

        //(1) Save photo
        $photo = save_photo($photo, '../image');

        $_db->beginTransaction();
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

        // (3) insert address record
        $stm = $_db->prepare('
        INSERT INTO Address (address_id, user_id, contact_name, contact_phone, complete_address, city, zipCode, state)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?);
        ');

        $stm->execute([$addressID, $id, $name,  $contact, $completeAddress, $city, $postal, $stateName]);

        $_db->commit();
        temp('info', 'You are registered succesfully');
        redirect('login.php');
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/member_registration.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="../js/photo.js"></script>
    <title>Member Registration</title>

    <script>
        function submitForm() {
            document.getElementById("state").submit();
        }
    </script>

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

        function validateEmail(){
            const input = document.getElementById("email");
            
        }

        
        //function to update the verifed status
        function updateStatus(isValid) {
            if (!isValid) {
                $('#verify').css('color', '90EE90');
            } else {
                $('#verify').css('color', 'grey');
            }
        }
    </script>
</head>

<body>


    <div class="container">
        <div class="title">Registration</div>
        <div class="content">
            <form action="" method="POST" id="state" enctype="multipart/form-data">
                <div class="user-details">


                    <div class="input-address">
                        <label class="details" for="photo">Profile Photo</label>
                        <label class="upload" tabindex="0" ondrop="upload_file(event)">
                            <?= generateFileField('photo', 'image/*', 'hidden') ?>
                            <img src="/image/photo.jpg" id="drag">
                        </label>
                        <?= err('photo') ?>
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
                        <span id="verify" class="fa">&#xf00c;Verified</span><a href="sendVerifyEmail.php">Verify Email</a><br />
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
                        <?= generatePassword('password', 'placeholder="Enter your password"  oninput="validatePassword()" required') ?>
                        <?= err('password'); ?>
                        <div id="validationMessage">
                            <ul style="font-size:13px;width:27em;list-style-type: none;padding-left:0;">
                                <li id="uppercase" class="invalid">❌ At least one uppercase letter (A-Z)</li>
                                <li id="lowercase" class="invalid">❌ At least one lowercase letter (a-z)</li>
                                <li id="number" class="invalid">❌ At least one number (0-9)</li>
                                <li id="special" class="invalid">❌ At least one special character (!@#$%^&*)</li>
                                <li id="length" class="invalid">❌ At least 8 characters long</li>
                            </ul>
                        </div>
                    </div>

                    <div class="input-box">
                        <label class="details" for="password">Confirm Password</label>
                        <!-- <input id="confirm" name="confirm" type="text" placeholder="Enter your password" required> -->
                        <?= generatePassword('confirm', 'placeholder="Enter again your password" required') ?>
                        <?= err('confirm') ?>
                    </div>

                    <div class="input-address">
                        <label class="details" for="address1">Address Line 1</label>
                        <!-- <input id="address1" name="address1" type="text" placeholder="Enter your password" required> -->
                        <?= generateTextField('address1') ?>
                        <?= err('address1') ?>
                    </div>

                    <div class="input-address">
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
                        <label class="details" for="city">City</label>
                        <?= displayCitiesForEachState($state) ?>
                        <?= err('city') ?>
                    </div>


                    <div class="button">
                        <input type="submit" value="Register">
                    </div>
                    <div class="button">
                        <a href="login.php">Cancel</a>
                    </div>

                </div>
            </form>
        </div>
    </div>


</body>

</html>