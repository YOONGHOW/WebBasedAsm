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
    $stm = $_db->prepare('
     SELECT 
      * 
      FROM 
      users
      where user_id = :user_id
');

    $stm->bindParam(':user_id', $userID);

    $stm->execute();

    $userInfo = $stm->fetch(PDO::FETCH_ASSOC);
}
$paymentMethods = [
    'C' => 'Bank Card',
    'F' => 'FPX',
    'P' => 'Paypal'
];
//  unset($_SESSION['selectedItems']);

$_err = [];
global  $email, $cardNumber, $expDate, $cvv, $cardholder, $paymentMethod,
    $paymentid, $shippingfee, $payementStatus, $ref_payment, $paymentAmount, $sub_payment;
$orderInserSucess = "";
date_default_timezone_set('Asia/Kuala_Lumpur');
$payment_date = date('Y-m-d'); // Current date
$payment_time = date('H:i:s'); // Current time

//get addressID
global $addressid, $address1, $state, $city, $zip, $contactname, $phonecontect;
$addressQuery = $_db->prepare('
           SELECT * from address where user_id=:user_id ');

$addressQuery->bindParam(':user_id', $userID);

$addressQuery->execute();
$addressIDDB = $addressQuery->fetch(PDO::FETCH_ASSOC);
if ($addressIDDB) {
    $addressid = (string)$addressIDDB['address_id'];
    $address1 = $addressIDDB['complete_address'];
    $state = $addressIDDB['state'];
    $city = $addressIDDB['city'];
    $zip = $addressIDDB['zipCode'];
    $contactname = $addressIDDB['contact_name'];
    $phonecontect = $addressIDDB['contact_phone'];
} else {
    $addressid = "null";
    $addressgetID = $_db->prepare('
           SELECT * from address order by address_id');


    $addressgetID->execute();
    $addressgetIDall = $addressgetID->fetchAll(PDO::FETCH_ASSOC);

    if (count($addressgetIDall) > 0) {
        $lastaddressId = $addressgetIDall[count($addressgetIDall) - 1]['address_id'];
        $nextaddressId = substr($lastaddressId, 1);
    }

    if ($nextaddressId <= 0) {
        $addressID = "A001";
    } else {
        $nextaddressId++;
        if ($nextaddressId < 10) {
            $addressID = "A00" . $nextaddressId;
        } else if ($nextaddressId < 100) {
            $addressID = "A0" . $nextaddressId;
        } else if ($nextaddressId < 1000) {
            $addressID = "A" . $nextaddressId;
        }
    }
}


if (is_post()) {
    $email = req('email');
    $paymentMethod = req('paymentMethod');
    $cardNumber = req('cardNumber');
    $expDate = req('expDate');
    $cvv = req('cvv');
    $cardholder = req('cardHolderName');
    $shippingfee = req('shiping');
    $paymentAmount = req('totalcal');

    if (isset($_POST['email'])) {
        if ($email == '') {
            $_err['email'] = 'Please enter the email';
        } else if (!preg_match("/^[A-Za-z0-9]+@[A-Za-z0-9\.]+$/", $email)) {
            $_err['email'] = 'Invalid email format';
        }
        if ($paymentMethod == '') {
            $_err['paymentMethod'] = 'Please select Payment Method';
        } else if (!array_key_exists($paymentMethod, $paymentMethods)) {
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

        if (empty($_err)) {

            $stm = $_db->prepare('SELECT payment_id FROM payment ORDER BY payment_id');

            $stm->execute();

            $result = $stm->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) > 0) {
                $lastPaymentId = $result[count($result) - 1]['payment_id'];
                $paymentidDB = substr($lastPaymentId, 1);
            }

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

            //get id

            $orderQuery = $_db->prepare('
            SELECT order_id from orders Order By order_id');
            $orderQuery->execute();
            $orderID = $orderQuery->fetchAll(PDO::FETCH_ASSOC);


            if (count($orderID) > 0) {
                $lastOrderId = $orderID[count($orderID) - 1]['order_id'];
                $orderDB = substr($lastOrderId, 1);
            }

            if ($orderDB <= 0) {
                $orderid = "O001";
            } else {
                $orderDB++;
                if ($orderDB < 10) {
                    $orderid = "O00" . $orderDB;
                } else if ($orderDB < 100) {
                    $orderid = "O0" . $orderDB;
                } else if ($orderDB < 1000) {
                    $orderid = "O" . $orderDB;
                }
            }



            //insert Order
            $orderInseret = $_db->prepare('
        Insert INTO orders (order_id, user_id, order_date, order_time, order_status, address_id)
        value(?,?,?,?,?,?)
        ');

            $orderInseret->bindParam(1, $orderid);
            $orderInseret->bindParam(2, $userID);
            $orderInseret->bindParam(3, $payment_date);
            $orderInseret->bindParam(4, $payment_time);
            $orderInseret->bindValue(5, 'S');
            $orderInseret->bindParam(6, $addressid);

            $orderInseret->execute();
            if ($orderInseret->rowCount() > 0) {

                $itemOrder = $_SESSION['selectedItems'];

                foreach ($itemOrder as $product) {
                    $orderbridge = $_db->prepare('
            Insert INTO orders_detail (order_id,product_id,product_quantity)
            value(?,?,?)
            ');

                    $orderbridge->bindParam(1, $orderid);
                    $orderbridge->bindParam(2, $product['id']);
                    $orderbridge->bindParam(3, $product['quantity']);
                    $orderbridge->execute();
                }
                $orderInserSucess = "True";
            }

            //insert bank card
            if ($paymentMethod == "C") {
                $bankCardInseret = $_db->prepare('
        Insert INTO bank_card (cardNumber, user_id, Card_vcc, Card_date, Card_holder)
        value(?,?,?,?,?)
        ');

                $bankCardInseret->bindParam(1, $cardNumber);
                $bankCardInseret->bindParam(2, $userID);
                $bankCardInseret->bindParam(3, $cvv);
                $bankCardInseret->bindParam(4, $currentYear);
                $bankCardInseret->bindParam(5, $cardholder);

                $bankCardInseret->execute();
            }


            //insert Payment
            $paymentInseret = $_db->prepare('
        Insert INTO payment (payment_id, order_id, tax_id, cardNumber, payment_date, payment_time,
        payment_amount,payment_shipping_fee,payment_method,payment_status)
        value(?,?,?,?,?,?,?,?,?,?)
        ');

            $paymentInseret->bindParam(1, $paymentid);
            $paymentInseret->bindParam(2, $orderid);
            $paymentInseret->bindValue(3, "T001");
            $paymentInseret->bindParam(4, $cardNumber);
            $paymentInseret->bindParam(5, $payment_date);
            $paymentInseret->bindParam(6, $payment_time);
            $paymentInseret->bindParam(7, $paymentAmount);
            $paymentInseret->bindParam(8, $shippingfee);
            $paymentInseret->bindParam(9, $paymentMethod);
            $paymentInseret->bindValue(10, "S");
            $paymentInseret->execute();




            $shipingQuery = $_db->prepare('
            SELECT  shipping_pacel_ref from shipping_detail order By shipping_pacel_ref');
            $shipingQuery->execute();
            $shippingID = $shipingQuery->fetchAll(PDO::FETCH_ASSOC);


            if (count($shippingID) > 0) {
                $lastShippingId = $shippingID[count($shippingID) - 1]['shipping_pacel_ref'];
                $shippingDB = substr($lastShippingId, 3);
            }

            if ($shippingDB <= 0) {
                $shippingid = "SHP001";
            } else {
                $shippingDB++;
                if ($shippingDB < 10) {
                    $shippingid = "SHP00" . $shippingDB;
                } else if ($shippingDB < 100) {
                    $shippingid = "SHP0" . $shippingDB;
                } else if ($shippingDB < 1000) {
                    $shippingid = "SHP" . $shippingDB;
                }
            }

            $shipmentInseret = $_db->prepare('
        Insert INTO shipping_detail (shipping_pacel_ref, order_id, shipping_company, shipping_status)
        value(?,?,?,?)
        ');

            $shipmentInseret->bindParam(1, $shippingid);
            $shipmentInseret->bindParam(2, $orderid);
            $shipmentInseret->bindValue(3, "null");
            $shipmentInseret->bindValue(4, "P");
            $shipmentInseret->execute();


            $alertMessage = "Successful make payment !"; // Customize your alert message
            $redirectUrl = "home.php";
            unset($_SESSION['selectedItems']);
            echo '<script type="text/javascript">';
            echo 'alert("' . addslashes($alertMessage) . '");'; // Show the alert
            echo 'window.location.href = "' . $redirectUrl . '";'; // Redirect to home page
            echo '</script>';
            unset($_SESSION['selectedItems']);

            exit();
        }
    }
}
global $itemOrder;
$ids = [];
$itemOrder = [];

if (isset($_SESSION['selectedItems'])) {
    foreach ($_SESSION['selectedItems'] as $item) {
        $ids[] = $item['id'];
    }
    $itemOrder = $_SESSION['selectedItems'];
    // Create a string of placeholders for the SQL statement
    if (count($ids) > 0) {
        $stm = $_db->prepare('
     SELECT 
        product.*,
        product_img.*
    FROM 
        product
    LEFT JOIN 
        product_img ON product.product_id = product_img.product_id
');
        $stm->execute();


        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
    }
}


if (is_post()) {
    // Retrieve form values
    if (isset($_POST['address'])) {
        $receiverName = $_POST['receivername'];
        $receiverPhone = $_POST['receiverphone'];
        $addressLine1 = $_POST['addressline1'];
        $state = $_POST['malaysia_state'];
        $city = $_POST['city'];
        $zipCode = $_POST['zipCode'];

        $stmt = $_db->prepare("INSERT INTO address (address_id, user_id, contact_name, contact_phone, complete_address, city, zipCode, state) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind each variable to the statement
        $stmt->bindParam(1, $addressID);
        $stmt->bindParam(2, $userID);
        $stmt->bindParam(3, $receiverName);
        $stmt->bindParam(4, $receiverPhone);
        $stmt->bindParam(5, $addressLine1);
        $stmt->bindParam(6, $city);
        $stmt->bindParam(7, $zipCode);
        $stmt->bindParam(8, $state);

        // Execute the statement
        $stmt->execute();
        header("Location: http://localhost:8000/web/PaymentPage.php");
    }
}
?>

<body>
    <div class="loadpage">
        <img src="../image/loading.gif" alt="loading">
        <p>Connection...</p>
    </div>
    <div class="paymentContainer" id="paymentContainer">

        <div class="Infocontainer">
            <div class="user_image">
                <img src="../image/<?= $userInfo['users_IMG_source'] ?>" alt="productImage">
                <input type="text" placeholder="userName" value="<?= $userInfo['user_name'] ?>">
            </div>

            <div class="productDetail">
                <div class="product_Name">
                    <input type="text" placeholder="productName" id="pname">
                    <input type="text" placeholder="productprice" id="pprice">
                </div>
                <div class="product_image">
                    <img src="https://picsum.photos/200/300" alt="productImage" id="pimage">
                </div>
            </div>
            <div class="checkOurList" id="checkOurList">
                <?php
                $countNum = 0;
                foreach ($itemOrder as $orderDetails) {
                    foreach ($result as $product) {
                        if ($product['product_id'] == $orderDetails['id']) {
                            $subtotal = $product['product_price'] * $orderDetails['quantity'];
                            $sub_payment += $subtotal;
                ?>

                            <div class="orderContainer container<?= $countNum ?>" id="orderContainer"
                                data-product-name="<?= $product['product_name'] ?>"
                                data-product-price="<?= $product['product_price'] ?>"
                                data-product-image="<?= $product['product_IMG_source'] ?>"
                                data-product-image-name="<?= $product['product_IMG_name'] ?>"
                                onclick="handleClick(this)">
                                <input type="text" value="<?= $product['product_name'] ?>" placeholder="productName" readonly>
                                <input type="text" value="RM <?= number_format($product['product_price'], 2) ?>" placeholder="productPrice" readonly>
                                <input type="text" value="<?= $orderDetails['quantity'] ?>x" placeholder="orderQuantity" readonly>
                                <input type="text" value="RM <?= number_format($subtotal, 2) ?>" placeholder="subTotal">
                            </div>

                <?php
                        }
                    }
                    $countNum++;
                } ?>
            </div>
        </div>
        <input type="hidden" id="recordIn" value="<?= $orderInserSucess ?>">
        <form method="POST" action="PaymentPage.php" id="paymentForm" name="paymentForm">
            <div class="paymentMethod">
                <div class="paymentWord">
                    <p>Payment Details</p>
                    <p>Complete your purchase by providing your payment details</p>
                </div>

                <div class="paymentDetail">
                    <label for="paymentEmail">
                        Email address
                    </label>
                    <!-- <input autofocus type="text" placeholder="Email" id="paymentEmail" name="paymentEmail"> -->
                    <?= generateTextField('email', 'class="login-input" autofocus placeholder="e.g. xxx@gmail.com" required') ?><br />

                    <p><?= err('email') ?></p>
                    <label for="paymentMethod">
                        Payment Method
                    </label>
                    <select name="paymentMethod" id="">
                        <?php
                        // Loop through the array to create options dynamically
                        foreach ($paymentMethods as $key => $method) {
                            $selected = ($method === 'Bank Card') ? 'selected' : '';
                            echo "<option value='$key' $selected>$method</option>";
                        }
                        ?>
                    </select>
                    <p><?= err('paymentMethod') ?></p>

                    <label for="cardDetail">
                        Card Details
                    </label>
                    <div class="carddetail">
                        <img src="https://cdn3.iconfinder.com/data/icons/payment-method-1/64/_Visa-512.png" alt="Banking Icon">
                        <!-- <input type="text" placeholder="Card Details" id="cardNumber" name="cardNumber">
                        <input type="text" placeholder="MM/YY" id="expDate" name="expDate" maxlength="5">
                        <input type="password" placeholder="CVV" id="cvv" name="cvv" maxlength="3">-->

                        <?= generateTextField('cardNumber', 'class="login-input" placeholder="Card Details" required') ?>

                        <?= generateTextField('expDate', 'class="login-input" placeholder="MM/YY" maxlength="5" required') ?>

                        <?= generatePassword('cvv', 'class="login-input" type="password" placeholder="CVV" maxlength="3" required') ?>

                    </div>
                    <p><?= err('card') ?></p>

                    <label for="cardHolderName">
                        Cardholder Name
                    </label>
                    <!-- <input type="text" placeholder="cardHolder Name" id="cardHolderName" name="cardHolderName"> -->

                    <?= generateTextField('cardHolderName', 'class="login-input" placeholder="Cardholder Name" required') ?><br />
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
                        <input type="text" id="subtotalcal" name="subtotalcal" value="RM <?= number_format($sub_payment, 2) ?>">
                    </div>
                    <div class="subtotal">
                        <label for="shiping">
                            Shipping Fee
                        </label>
                        <input type="text" id="shiping" name="shiping" value="RM 4.90">
                    </div>
                    <div class="discountcal">
                        <label for="discountcal">
                            Discount
                        </label>
                        <input type="text" id="discountcal" name="discountcal" value="RM 0.00">
                    </div>
                    <div class="total">
                        <label for="totalcal">
                            Total
                        </label>
                        <input type="text" id="totalcal" name="totalcal" value="RM <?= number_format($sub_payment + 4.9, 2) ?>">
                    </div>
                </div>
                <div class="changeaddress">
                    <a id="openmodal" style="display: none;">Change Delive r Address</a>
                </div>


                <div class="paybutton">
                    <button class="paymentbtn" id="payment_add" name="payment_add" onclick="checkAddressAndSubmit()" type="button">Pay RM<?= number_format($sub_payment + 4.9, 2)  ?></button>
                </div>
            </div>
        </form>
    </div>
    <div class="modelwindows" id="modelwindows">
        <form action="" method="post" class="modelAddress">
            <div class="modelherder">
                <p>
                    Change Deliver Address
                    <input type="hidden" id="addressID" value="<?= $addressid ?>">
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
                        <input type="text" placeholder="Name" id="receivername" name="receivername" value="<?= $contactname ?>">
                        <input type="text" placeholder="Phone Number" id="receiverphone" name="receiverphone" value="<?= $phonecontect ?>">
                    </div>
                </div>
                <label for="addressline1">
                    Address line 1
                </label>
                <input type="text" placeholder="Address line1" id="addressline1" name="addressline1" value="<?= $address1 ?>">
                <label for="State">
                    State
                </label>
                <select name="malaysia_state" id="malaysia_state">
                    <option value="" disabled>Select State</option>
                    <?php

                    // Loop through the states to create options
                    foreach ($states as $key => $value) {
                        // Check if the current state matches the one stored in the database
                        $selected = ($key === $state) ? 'selected' : '';
                        echo "<option value='$key' $selected>$value</option>";
                    }
                    ?>
                </select>
                <div class="form-group">
                    <div class="labels">
                        <label for="city">City</label>
                        <label for="zipCode">Zip Code</label>
                    </div>

                    <div class="inputs">
                        <input type="text" placeholder="City" id="city" name="city" value="<?= $city ?>">
                        <input type="text" placeholder="Zip Code" id="zipCode" name="zipCode" value="<?= $zip ?>">
                    </div>
                </div>
                <div class="modelbutton">
                    <button>Cancel</button>
                    <button type="submit" name="address">Submit</button>
                </div>

            </div>
        </form>
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

    var selectedContainer = null;

    function handleClick(element) {
        // Get the product name and price from the data attributes
        var productname = element.getAttribute('data-product-name');
        var productprice = element.getAttribute('data-product-price');
        var productImage = element.getAttribute('data-product-image');
        var productImageName = element.getAttribute('data-product-image-name');
        if (selectedContainer) {
            selectedContainer.style.outline = "none"; // Reset the border of the previously selected element
        }

        // Set the border for the clicked element
        element.style.outline = "2px solid black";
        selectedContainer = element;
        document.getElementById('pname').value = productname;
        document.getElementById('pprice').value = "RM " + productprice;
        document.getElementById('pimage').src = "../image/" + productImage;
        document.getElementById('pimage').alt = productImageName;
    }



    function checkAddressAndSubmit() {
        // Get the address input value
        var addressID = document.getElementById('addressID').value;

        // Check if the address is empty
        if (addressID == "null") {
            // Display the modal if the address is empty
            modal.style.display = "block";
            console.log(modal.style.display);
        } else {
            // Submit the form if the address is not empty
            document.getElementById('paymentForm').submit();
        }
    }

    const countNum = <?= json_encode($countNum) ?>;
    console.log(countNum);
    window.onload = function() {
        if (countNum >= 3) {
            const checkOurListElement = document.getElementById('checkOurList');
            if (checkOurListElement) {
                checkOurListElement.style.overflowY = 'scroll';
            }
        }

        var firstContainer = document.querySelector('.orderContainer');
        if (firstContainer) {
            firstContainer.click(); // Trigger the click event
        }
    }
</script>

</html>