<!-- Php-->
<?php include "header.php"; ?>
<?php
global $_user;
$_user = $_SESSION['user'] ?? null;

if ($_user == null) {
    echo "<script>alert('You must login as member first')
    window.location.href = 'home.php';
    </script>";
} else {
    $userID = $_user->user_id;
}

//store the selection of user

if (isset($_POST['selectedItems'])) {
    foreach ($_POST['selectedItems'] as $item) {
        if ($item !== "on") {
            // Explode the value into price and quantity
            list($id, $quantity) = explode(",", $item);

            $_SESSION['selectedItems'][] = [
                'id' => $id,
                'quantity' => $quantity
            ];
        }
    }
header("Location: http://localhost:8000/web/PaymentPage.php");

}

$ship_fee = 4.9;
$discount = 0;
$total_price = 0;
$total_payment = 0;

try {
    if (isset($_POST['deleteBtn'])) {
            $product_id = $_POST["productID"];

            $stmt = $_db->prepare("
            DELETE FROM cart WHERE product_id = :product_id AND user_id = :user_id
            ");

            $stmt->bindParam(':user_id', $userID);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
            $carts = $stmt->fetchAll();
            header("Location: cart.php");
            exit;
        
    }

    $stmt = $_db->prepare("
        SELECT cart.cart_id, cart.product_id, cart.quantity, product.*, product_img.*
        FROM cart
        JOIN product ON cart.product_id = product.product_id
        LEFT JOIN product_img ON product.product_id = product_img.product_id
        WHERE cart.user_id = :user_id
    ");

    $stmt->bindParam(':user_id', $userID);
    $stmt->execute();
    $carts = $stmt->fetchAll();
?>

    <!-- JavaScript-->

    <script>
        function updateTotal() {
            let totalPrice = 0;
            var selectedItems = document.querySelectorAll('.selectItem:checked');

            selectedItems.forEach(item => {
                var price = parseFloat(item.getAttribute('data-price'));
                var quantityInput = item.closest('.cart_container').querySelector('.quantityInput');
                var quantity = parseInt(quantityInput.value);

                totalPrice += price * quantity;
            });

            // Update total price display
            document.getElementById('totalPrice').innerText = 'RM ' + totalPrice.toFixed(2);

            var shippingFee = 4.9;
            var totalPayment = totalPrice + shippingFee;
            document.getElementById('totalPayment').innerText = 'RM ' + totalPayment.toFixed(2);
        }

        //Select all checked
        function toggle(source) {
            var checkboxes = document.getElementsByName('selectedItems[]');
            for (let i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = source.checked;
            }
            updateTotal();
        }
    </script>

    <!-- htmlt-->

    <main style="min-height: 500px;">

        <span style="display: flex; align-items: center; margin-left:670px; padding:15px;">
            <img src="../image/shopping-cart.png" alt="cart" style="margin-right: 10px; width:30px; height:30px;">
            <h1 style="margin: 0;">My Cart</h1>
        </span>

            <span style="display: flex;align-items: center;">
                <input type="checkbox" name="selectAll" id="selectAll" onclick="toggle(this)" /> &nbsp;&nbsp;All</span><br>

            <section class="cart_section">
                <nav class="cart_side">

                    <ul>
                        <?php
                         if($carts == null){
                            echo "<p style='font-size:20px; margin-top:100px;'>No record found(s). Go to find your suitable product and add it to cart😁</p>";
                        }
                        foreach ($carts as $cart) {
                           
                            $product_img = "../image/" . $cart->product_IMG_name;

                        ?>
                            <form method="POST" action="cart.php">
                            <div class="cart_container">
                            <div class="cart-feature">
                            
                                <input type="checkbox" name="selectedItems[]" id="selectOne" class="selectItem"
                                    data-price="<?= $cart->product_price ?>"
                                    data-quantity="<?= $cart->quantity ?>"
                                    value="<?= $cart->product_id ?>,<?= $cart->quantity ?>"
                                    onchange="updateTotal()" />
                                    <input type="hidden" name="productID" value="<?= $cart->product_id ?>">
                                    <button type="submit" name="deleteBtn" id="dlt_btn"><img src="../image/delete.png" class="cart_btn_img"></button>
                                 <button name="editBtn" id="edit_btn"><img src="../image/pencil.png" class="cart_btn_img2"></button>
                                 </div>

                                <div class="cart_image_box">
                                    <img src="../image/<?= $product_img ?>" alt="<?= $cart->product_name ?>">
                                </div>
                                <div class="cart_details_box">
                                    <h1><?= $cart->product_name ?></h1><br>
                                    <p>Price: RM<?= number_format($cart->product_price, 2) ?></p><br>
                                    <p>Quantity: <span id="quantity_display"><?= $cart->quantity ?></span>
                                    <input type="hidden" name="quantity" value="<?= $cart->quantity ?>"
                                        min="1" max="<?= $cart->product_stock ?>"
                                        class="quantityInput"
                                        data-price="<?= $cart->product_price ?>"
                                        oninput="updateTotal()" style="width:25%;" /> <br>

                                    <p>Total: RM<?= number_format($cart->product_price * $cart->quantity, 2) ?></p><br>
                                </div>
                            </div>
                            

                    <?php
                        }
                    } catch (PDOException $e) {
                        echo 'Error: ' . $e->getMessage();
                    }
                    ?>
                    </ul>
                </nav>

                <div class="cart_total_container">
                    <div class="payment-details">
                        <p class="label">Total:</p>
                        <p id="totalPrice">RM <?= number_format($total_price, 2) ?></p>
                    </div><br>

                    <div class="payment-details">
                        <p class="label">Shipping Fee:</p>
                        <p>RM <?= number_format($ship_fee, 2) ?></p>
                    </div><br>

                    <div class="payment-details">
                        <p class="label">Voucher Discount:</p>
                        <p>RM <?= number_format($discount, 2) ?></p>
                    </div><br>

                    <div class="payment-details">
                        <p class="label">Total Payment:</p>
                        <p id="totalPayment">RM <?= number_format($total_payment, 2) ?></p>
                    </div>

                    <input type="submit" name="checkOutBtn" id="checkOutBtn" value="Place Order" />
                </div>

                </form>
                </div>
            </section>
            <br>


    </main>
    <?php include "footer.php"; ?>