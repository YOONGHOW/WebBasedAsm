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

    if (isset($_POST['quantity']) && isset($_POST['productID'])) {
        $product_id = $_POST["productID"];
        $new_quantity = $_POST["quantity"];
        
        $stmt = $_db->prepare("
            UPDATE cart SET quantity = :quantity WHERE product_id = :product_id AND user_id = :user_id
        ");

        $stmt->bindParam(':quantity', $new_quantity);
        $stmt->bindParam(':user_id', $userID);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
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

                            <script>
                                function myFunction(button) {
                                    var cartContainer = button.closest('.cart_container');

                                    var x = cartContainer.querySelector('.quantity');
                                    var y = cartContainer.querySelector('.edit_quantity');

                                    var editBtn = cartContainer.querySelector('.edit_btn');
                                    var saveBtn = cartContainer.querySelector('.save_btn');

                                    if (y.style.display === "none" || y.style.display === "") {
                                        x.style.display = "none";          
                                        y.style.display = "block";         
                                        editBtn.style.display = "none";    
                                        saveBtn.style.display = "inline-block"; 
                                    } else {
                                        x.style.display = "block";         
                                        y.style.display = "none";          
                                        editBtn.style.display = "inline-block"; 
                                        saveBtn.style.display = "none";    
                                    }
                                }

                                function saveQuantity(button) {
                                    var cartContainer = button.closest('.cart_container');

                                    var quantityInput = cartContainer.querySelector('.edit_quantity input');
                                    var newQuantity = parseInt(quantityInput.value);

                                    cartContainer.querySelector('input[name="quantity"]').value = newQuantity;
                                    var form = cartContainer.querySelector('form');
                                    form.submit(); 
                                }
                            </script>

                            <div class="cart_container">
                            <div class="cart-feature">
                            
                                <input type="checkbox" name="selectedItems[]" id="selectOne" class="selectItem"
                                    data-price="<?= $cart->product_price ?>"
                                    data-quantity="<?= $cart->quantity ?>"
                                    value="<?= $cart->product_id ?>,<?= $cart->quantity ?>"
                                    onchange="updateTotal()" />
                                    <input type="hidden" name="productID" value="<?= $cart->product_id ?>">
                                    <button type="submit" name="deleteBtn" id="dlt_btn"><img src="../image/delete.png" class="cart_btn_img"></button>
                                    <button type="button" name="editBtn" id="edit_btn" class="edit_btn" onclick="myFunction(this)"><img src="../image/pencil.png" class="cart_btn_img2"></button>
                                    
                                    <button type="submit" name="saveBtn" id="save_btn" class="save_btn" onclick="saveQuantity(this)"  style="display: none;">
                                        <img src="../image/save.png" class="cart_btn_img">
                                    </button>

                                    
                                </div>

                                <div class="cart_image_box">
                                    <img src="../image/<?= $product_img ?>" alt="<?= $cart->product_name ?>">
                                </div>
                                <div class="cart_details_box">
                                    <h1><?= $cart->product_name ?></h1><br>
                                    <p>Price: RM<?= number_format($cart->product_price, 2) ?></p><br>

                                    <span class="quantity" style="display: block;"><p>Quantity:<?= $cart->quantity ?></p></span>
                                    <span class="edit_quantity" style="display: none;"><p>Quantity: <input type="number" value="<?= $cart->quantity ?>" name="editQty" style="width:30%;" min="1" max="<?= $cart->product_stock?>"></p></span>

                                    <input type="hidden" name="quantity" value="<?= $cart->quantity ?>"
                                        min="1" max="<?= $cart->product_stock ?>"
                                        class="quantityInput"
                                        data-price="<?= $cart->product_price ?>"
                                        oninput="updateTotal()" style="width:25%;" />

                                        <br><p>Total: RM<?= number_format($cart->product_price * $cart->quantity, 2) ?></p><br>
                                </div>
                            </div>
                            </form>

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
                    <form method="POST" action="cart.php">
                    <input type="submit" name="checkOutBtn" id="checkOutBtn" value="Place Order" />
                </form>
                </div>


                </div>
            </section>
            <br>


    </main>
    <?php include "footer.php"; ?>