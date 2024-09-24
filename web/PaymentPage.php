<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/payment_design.css">
    <link rel="stylesheet" type="text/css" href="/css/modelwindow.css">

    <title>Document</title>
</head>
<?php

require "../helperFile/helper.php";

global $_user;
$_user = $_SESSION['user'] ?? null;

if ($_user == null) {
    echo "<script>alert('You must login as member first')
    window.location.href = 'home.php';
    </script>";
} else {
    $userID = $_user->user_id;
}
$ids = [];
$itemOrder = [];

if (isset($_SESSION['selectedItems'])) {
    foreach ($_SESSION['selectedItems'] as $item) {
        $ids[] = $item['id'];
    }
    $itemOrder = $_SESSION['selectedItems'];
    print_r(implode(',', array_fill(0, count($ids), '?')));
    // Create a string of placeholders for the SQL statement
    if (count($ids) > 0) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stm = $_db->prepare('
     SELECT 
        product.*,
        product_img.*
    FROM 
        product
    LEFT JOIN 
        product_img ON product.product_id = product_img.product_id
    WHERE 
        product_id IN ($placeholders)
');
$idString=implode(",",$ids);
        $stm->execute();

        $stm->execute();

        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
    }
}




$paymentMethods = [
    'C' => 'Bank Card',
    'F' => 'FPX',
    'P' => 'Paypal'
];

$_err = [];
global  $email, $cardNumber, $expDate, $cvv, $cardholder, $paymentMethod,
    $paymentid, $shippingfee, $payementStatus, $ref_payment, $paymentAmount;
date_default_timezone_set('Asia/Kuala_Lumpur');
$payment_date = date('Y-m-d'); // Current date
$payment_time = date('H:i:s'); // Current time

if (is_post()) {
    $email = req('paymentEmail');
    $paymentMethod = req('paymentMethod');
    $cardNumber = req('cardNumber');
    $expDate = req('expDate');
    $cvv = req('cvv');
    $cardholder = req('cardHolderName');
    $shippingfee = req('shiping');
    $paymentAmount = req('totalcal');

    if ($email == '') {
        $_err['email'] = 'Please enter the email';
    } else if (!preg_match("/^[A-Za-z0-9]+@[A-Za-z0-9\.]+$/", $email)) {
        $_err['email'] = 'Invalid email format';
    }
    if ($paymentMethod == '') {
        $_err['paymentMethod'] = 'Please select Payment Method';
    } else if (!in_array($paymentMethod, $paymentMethods)) {
        $_err['paymentMethod'] = 'Please select valid Payment Method';
    }

    if ($cardNumber == '') {
        $_err['card'] = 'Please enter Card Number';
    } elseif (!preg_match('/^\d{16}$/', $cardNumber)) {
        $_err['card'] = 'Please enter a valid Card Number (16 digits)';
    } else {
        // If card number is valid, check the expiration date
        if ($expDate == '') {
            $_err['card'] = 'Please enter the Expiration date of the card';
        } elseif (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expDate, $matches)) {
            $_err['card'] = 'Please enter a valid Expiration date of the card (MM/YY)';
        } else {
            // Extract month and year from the matched pattern
            $expMonth = $matches[1]; // MM part
            $expYear = $matches[2];  // YY part

            // Get the current month and year
            $currentYear = (int)date("y");  // Current year in YY format
            $currentMonth = (int)date("m"); // Current month in MM format
            $expYearFull = 2000 + (int)$expYear; // Convert 2-digit year to 4-digit year (e.g., 23 becomes 2023)

            // Check if the card is expired
            if ($expYearFull < (int)date("Y") || ($expYearFull == (int)date("Y") && (int)$expMonth < $currentMonth)) {
                $_err['card'] = "The card is expired.";
            }
            if ($cvv == '') {
                $_err['card'] = 'Please enter CVV';
            } else if (!preg_match('/^\d{3}$/', $cvv)) {
                $_err['card'] = 'Please enter valid CVV (In 3 digit)';
            }
        }
    }
    if ($cardholder == '') {
        $_err['holder'] =  "Please enter Card Holder Name";
    } else if (strlen($cardholder) >= 200) {
        $_err['holder'] =  "Card Holder Name is limit in 200 words";
    }
    //  if (empty($_err)) {

    $stm = $_db->prepare('SELECT payment_id FROM payment');

    $stm->execute();

    $result = $stm->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        $lastPaymentId = $result[count($result) - 1]['payment_id'];
        $paymentidDB = substr($lastPaymentId, 1);
    }

    $paymentidDB++;
    if ($paymentidDB <= 0) {
        $paymentid = "P001";
    } else {
        $paymentidDB++;
        if ($paymentidDB < 10) {
            $paymentid = "P00" . $paymentidDB;
        } else if ($paymentidDB < 100) {
            $paymentid = "P0" . $paymentidDB;
        } else if ($paymentidDB < 1000) {
            $paymentid = "P" . $paymentidDB;
        }
    }

    // Embedding JavaScript in PHP to trigger the popup
    echo "<script type='text/javascript'>alert('$paymentid');</script>";
    //   }
}


?>

<body>
    <div class="paymentContainer" id="paymentContainer">
        <div class="Infocontainer">

            <div class="user_image">
                <img src="https://picsum.photos/200/300" alt="productImage">
                <input type="text" placeholder="userName">
            </div>

            <div class="productDetail">
                <div class="product_Name">
                    <input type="text" placeholder="productName">
                    <input type="text" placeholder="productprice">
                </div>
                <div class="product_image">
                    <img src="https://picsum.photos/200/300" alt="productImage">
                </div>
            </div>

            <div class="checkOurList">
                <div class="orderContainer">
                    <input type="text" placeholder="productName">
                    <input type="text" placeholder="productPrice">
                    <input type="text" placeholder="orderQuantity">
                    <input type="text" placeholder="subTotal">
                </div>
            </div>

        </div>
        <form method="POST" action="">
            <div class="paymentMethod">
                <div class="paymentWord">
                    <p>Payment Details</p>
                    <p>Complete your purchase by providing your payment details</p>
                </div>

                <div class="paymentDetail">
                    <label for="paymentEmail">
                        Email address
                    </label>
                    <input autofocus type="text" placeholder="Email" id="paymentEmail" name="paymentEmail">
                    <p><?= err('email') ?></p>
                    <label for="paymentMethod">
                        Payment Method
                    </label>
                    <select name="paymentMethod" id="paymentMethod">
                        <?php
                        // Loop through the array to create options dynamically
                        foreach ($paymentMethods as $key => $method) {
                            echo "<option value='$key'>$method</option>";
                        }
                        ?>
                    </select>
                    <p><?= err('paymentMethod') ?></p>

                    <label for="cardDetail">
                        Card Details
                    </label>
                    <div class="carddetail">
                        <img src="https://cdn3.iconfinder.com/data/icons/payment-method-1/64/_Visa-512.png" alt="Banking Icon">
                        <input type="text" placeholder="Card Details" id="cardNumber" name="cardNumber">
                        <input type="text" placeholder="MM/YY" id="expDate" name="expDate" maxlength="5">
                        <input type="password" placeholder="CVV" id="cvv" name="cvv" maxlength="3">
                    </div>
                    <p><?= err('card') ?></p>

                    <label for="cardHolderName">
                        Cardholder Name
                    </label>
                    <input type="text" placeholder="cardHolder Name" id="cardHolderName" name="cardHolderName">
                    <p><?= err('holder') ?></p>

                </div>

                <div class="paymentCalculation">
                    <div class="discount">
                        <label for="discount">
                            Discount code (Optional)
                        </label>
                        <input type="text" id="discount" name="discount">
                    </div>
                    <div class="subtotal">
                        <label for="subtotalcal">
                            Subtotal
                        </label>
                        <input type="text" id="subtotalcal" name="subtotalcal">
                    </div>
                    <div class="subtotal">
                        <label for="shiping">
                            Shipping Fee
                        </label>
                        <input type="text" id="shiping" name="shiping">
                    </div>
                    <div class="discountcal">
                        <label for="discountcal">
                            Discount
                        </label>
                        <input type="text" id="discountcal" name="discountcal">
                    </div>
                    <div class="total">
                        <label for="totalcal">
                            Total
                        </label>
                        <input type="text" id="totalcal" name="totalcal">
                    </div>
                </div>
                <div class="changeaddress">
                    <a id="openmodal">Change Deliver Address</a>
                </div>
                <div class="paybutton">
                    <button class="paymentbtn" type="submit">Pay RM123</button>
                </div>
            </div>
        </form>

    </div>
    <div class="modelwindows" id="modelwindows">
        <div class="modelherder">
            <p>
                Change Deliver Address
            </p>
            <a id="closemodal"><img src="https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-close-round-512.png" alt="close"></a>
        </div>
        <div class="addressConainer">
            <div class="form-group">
                <div class="labels receiver">
                    <label for="receivername">Name</label>
                    <label for="receiverphone">Phone Number</label>
                </div>

                <div class="inputs receiverInput">
                    <input type="text" placeholder="Name" id="receivername" name="receivername">
                    <input type="text" placeholder="Phone Number" id="receiverphone" name="receiverphone">
                </div>
            </div>
            <label for="addressline1">
                Address line 1
            </label>
            <input type="text" placeholder="Address line1" id="addressline1" name="addressline1">
            <label for="addressline2">
                Address line 2 (Optional)
            </label>
            <input type="text" placeholder="Address line2" id="addressline1" name="addressline2">
            <label for="State">
                State
            </label>
            <select name="malaysia_state" id="malaysia_state">
                <option value="johor">Johor</option>
                <option value="kedah">Kedah</option>
                <option value="kelantan">Kelantan</option>
                <option value="melaka">Malacca (Melaka)</option>
                <option value="negeri_sembilan">Negeri Sembilan</option>
                <option value="pahang">Pahang</option>
                <option value="penang">Penang (Pulau Pinang)</option>
                <option value="perak">Perak</option>
                <option value="perlis">Perlis</option>
                <option value="selangor">Selangor</option>
                <option value="terengganu">Terengganu</option>
                <option value="sabah">Sabah</option>
                <option value="sarawak">Sarawak</option>
            </select>
            <div class="form-group">
                <div class="labels">
                    <label for="city">City</label>
                    <label for="zipCode">Zip Code</label>
                </div>

                <div class="inputs">
                    <input type="text" placeholder="City" id="city" name="city">
                    <input type="text" placeholder="Zip Code" id="zipCode" name="zipCode">
                </div>
            </div>
            <div class="modelbutton">
                <button>Cancel</button>
                <button type="submit">Submit</button>
            </div>

        </div>
    </div>
</body>
<script>
    var openmodal = document.getElementById("openmodal");
    var closemodal = document.getElementById("closemodal");
    var modal = document.getElementById("modelwindows");

    openmodal.addEventListener("click", function() {
        modal.style.display = "block";
    });

    closemodal.addEventListener("click", function() {
        modal.style.display = "none";
    });

    window.addEventListener("click", function(event) {
        if (event.target != modal && event.target != openmodal) {
            modal.style.display = "none";
        }
    });
</script>

</html>