<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/order_History.css">
    <title>Document</title>
</head>
<?php
include "header.php";
?>
<?php
// Check if 'search' parameter is present in the URL
if (isset($_GET['search'])) {
    // Get the value of the 'search' parameter
    $search = $_GET['search'];
    if ($search == 'all') {
        $stm = $_db->prepare('SELECT * FROM orders');
    } else if ($search == 'toPay') {
        $stm = $_db->prepare('SELECT *
    FROM orders
    WHERE order_status = "P" AND order_id IN (
        SELECT order_id
        FROM payment
        WHERE payment_status = "N"
    )');
    } else if ($search == 'toShip') {
        $stm = $_db->prepare('SELECT *
    FROM orders
    WHERE order_status = "S" AND order_id IN (
        SELECT order_id
        FROM Shipping_Detail 
        WHERE shipping_status = "P" 
    )');
    } else if ($search == 'complete') {
        $stm = $_db->prepare('SELECT *
    FROM orders
    WHERE order_status = "S" AND order_id IN (
        SELECT order_id
        FROM Shipping_Detail 
        WHERE shipping_status = "R" 
    )');
    } else if ($search == 'cancelled') {
        $stm = $_db->prepare('SELECT *
    FROM orders
    WHERE order_status = "C" 
    ');
    } else if ($search == 'refund') {
        $stm = $_db->prepare('
        SELECT *
        FROM Orders
        WHERE order_id IN (
            SELECT order_id FROM return_refund
        )
    ');
    }
    $stm->execute();

    $result = $stm->fetchAll(PDO::FETCH_ASSOC);
    print_r($result);
} else {
    // If 'search' parameter is not found, output a message
    echo "No search parameter found in the URL.";
}
?>

<body>
    <div class="hisContainer">
        <?php include 'PaymentNav.php' ?>
        <div class="deliverHis">
            <div class="deliverDetail ">
                <a><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                        <path d="M48 0C21.5 0 0 21.5 0 48L0 368c0 26.5 21.5 48 48 48l16 0c0 53 43 96 96 96s96-43 96-96l128 0c0 53 43 96 96 96s96-43 96-96l32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-64 0-32 0-18.7c0-17-6.7-33.3-18.7-45.3L512 114.7c-12-12-28.3-18.7-45.3-18.7L416 96l0-48c0-26.5-21.5-48-48-48L48 0zM416 160l50.7 0L544 237.3l0 18.7-128 0 0-96zM112 416a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm368-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z" />
                    </svg> Parcel has been delivered </a>
            </div>
            <div class="deliverState">
                <p><?php
                $resual
                ?></p>
            </div>
        </div>


        <div class="productDetail">
            <div class="product">
                <div class="productIMg">
                    <img src="https://picsum.photos/200/300" alt="productImage">
                </div>
                <div class="productName">
                    <textarea rows="2">name</textarea>
                </div>
                <div class="quantity">
                    <input type="text" placeholder="quantity">
                </div>
                <div class="productPrice">
                    <input type="text" placeholder="price">
                </div>
            </div>
            <div class="product">
                <div class="productIMg">
                    <img src="https://picsum.photos/200/300" alt="productImage">
                </div>
                <div class="productName">
                    <textarea rows="2">name</textarea>
                </div>
                <div class="quantity">
                    <input type="text" placeholder="quantity">
                </div>
                <div class="productPrice">
                    <input type="text" placeholder="price">
                </div>
            </div>
        </div>


        <div class="deliverHis">
            <div class="deliverDetail ">
                <a><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                        <path d="M48 0C21.5 0 0 21.5 0 48L0 368c0 26.5 21.5 48 48 48l16 0c0 53 43 96 96 96s96-43 96-96l128 0c0 53 43 96 96 96s96-43 96-96l32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-64 0-32 0-18.7c0-17-6.7-33.3-18.7-45.3L512 114.7c-12-12-28.3-18.7-45.3-18.7L416 96l0-48c0-26.5-21.5-48-48-48L48 0zM416 160l50.7 0L544 237.3l0 18.7-128 0 0-96zM112 416a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm368-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z" />
                    </svg> Parcel has been delivered </a>
            </div>
            <div class="deliverState">
                <p>COMPLETED</p>
            </div>
        </div>


        <div class="productDetail">
            <div class="product">
                <div class="productIMg">
                    <img src="https://picsum.photos/200/300" alt="productImage">
                </div>
                <div class="productName">
                    <textarea rows="2">name</textarea>
                </div>
                <div class="quantity">
                    <input type="text" placeholder="quantity">
                </div>
                <div class="productPrice">
                    <input type="text" placeholder="price">
                </div>
            </div>
            <div class="product">
                <div class="productIMg">
                    <img src="https://picsum.photos/200/300" alt="productImage">
                </div>
                <div class="productName">
                    <textarea rows="2">name</textarea>
                </div>
                <div class="quantity">
                    <input type="text" placeholder="quantity">
                </div>
                <div class="productPrice">
                    <input type="text" placeholder="price">
                </div>
            </div>
        </div>
    </div>

</body>


<?php
include "footer.php";
?>


</html>