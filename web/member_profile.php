<!DOCTYPE html>
<html lang="en">

<?php

require '../helperFile/helper.php';

//validation part
$_err = [];
global $_user, $name, $ic, $date, $gender, $confirm, $password, $contact, $state, $city, $completeAddress, $postal, $photo;

$_user = $_SESSION['user'] ?? null;


if ($_user) {
    //sql to retrieve user address
    $sql = 'SELECT * FROM address WHERE user_id = :user_id';
    $stmt = $_db->prepare($sql);
    $stmt->execute(['user_id' => $_user->user_id]);
    $address = $stmt->fetch();

    if ($address) {
        $completeAddress = $address->complete_address;
        $postal = $address->zipCode;
        $state = $address->state;
        $city = $address->city;
    } else {
        $completeAddress = "none";
    }

    $name = $_user->user_name;
    $email = $_user->Email;
    $ic = $_user->user_IC;
    $contact = $_user->user_phoneNumber;
    $gender = $_user->user_gender;
    $photo = $_user->users_IMG_source;

}

if (is_post()) {

    $photo = get_file('photo');
    $createDate = date("d/m/Y");
    $name = req("name");
    $date = req("date");
    $gender = req('gender');
    $contact = req("contact");
    $address1 = req("completeAddress");
    $postal = req("postal");
    $state = req('state');
    $city = req("city");

    if (checkImage($photo)) {
        $_err['photo'] = checkImage($photo);
    }

    if (checkName($name) !== null) {
        $_err['name'] = checkName($name);
    }

    // if (checkIC($ic) !== null) {
    //     $_err['ic'] = checkIC($ic);
    // }

    // if (checkGmail($email) !== null) {
    //     $_err['email'] = checkGmail($email);
    // }

    // if (checkPassword($password) !== null) {
    //     $_err['password'] = checkPassword($password);
    // }

    // if (confirmPassword($password, $confirm) !== null) {
    //     $_err['confirm'] = confirmPassword($password, $confirm);
    // }

    // if (checkDateFormat($date) !== null) {
    //     $_err['date'] = checkDateFormat($date);
    // }

    if (strcmp($gender, "none") == 0) {
        $_err['gender'] = "Please select your gender.";
    }

    if (strcmp($state, "none") == 0) {
        $_err['state'] = "Please select your state.";
    }

    if (strcmp($city, "none") == 0) {
        $_err['city'] = "Please select your city.";
    }

    if (checkAddress($completeAddress) != null) {
        $_err['completeAddress'] = checkAddress($completeAddress);
    }

    if (checkPostal($postal) != null) {
        $_err['postal'] = checkPostal($postal);
    }

if(empty($_err)){

        $sql = 'UPDATE users 
            SET user_name = :name,
            user_phoneNumber = :contact, 
            user_gender = :price, 
            product_stock = :stock, 
            product_description = :description 
            WHERE product_id = :productId';

        $stmt = $_db->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'category' => $category,
            'price' => $price,
            'stock' => $stock,
            'description' => $description,
            'productId' => $productId
        ]);


    temp('info', 'You are Update succesfully');
    redirect('home.php');
}




    
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/member_registration.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../js/photo.js"></script>
    <title>Member Profile</title>

    <script>
        function submitForm() {
            document.getElementById("state").submit();
        }
    </script>

</head>

<body>

    <div class="container">
        <div class="title">Profile</div>
        <div class="content">
            <form action="" method="POST" id="state" enctype="multipart/form-data">
                <div class="user-details">

                    <div class="input-address">
                        <label class="details" for="photo">Profile Photo</label>
                        <label class="upload" tabindex="0" ondrop="upload_file(event)">
                            <?= generateFileField('photo', 'image/*', 'hidden') ?>
                            <img src="/image/<?=$photo ?>" id="drag">
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
                        <?= generateTextField('email', 'maxlength="100"  placeholder="e.g. xxx@gmail.com" disabled="disabled"') ?>
                        <?= err('email') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="ic">IC Number</label>
                        <!-- <input id="ic" name="ic" type="text" placeholder="010203020321" required> -->
                        <?= generateTextField('ic', 'maxlength="12" placeholder="e.g. 010203020321" disabled="disabled"') ?>
                        <?= err('ic') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="contact">Phone Number</label>
                        <!-- <input id="phone" name="phone" type="text" placeholder="012-1234567" required> -->
                        <?= generateTextField('contact', 'maxlength="11" placeholder="e.g. 0121234567" required') ?>
                        <?= err('contact') ?>
                    </div>

                    <div class="input-box">
                        <label class="details" for="gender">Gender</label>
                        <?= displayGenderList() ?>
                        <?= err('gender') ?>
                    </div>

                    <div class="input-address">
                        <label class="details" for="address1">Address</label>
                        <!-- <input id="address1" name="address1" type="text" placeholder="Enter your password" required> -->
                        <?= generateTextField('completeAddress') ?>
                        <?= err('completeAddress') ?>
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
                        <input type="submit" value="Update">
                    </div>
                    <div class="button">
                        <a href="home.php">Cancel</a>
                    </div>

                </div>
            </form>
        </div>
    </div>


</body>

</html>