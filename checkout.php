<?php
session_start();
include ('includes/connection.php');

if (!isset($_SESSION['logged_in']) && (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0)) {
    header('Location:login.php?error=Please log in first or add items to the cart');
    exit();
}
if($_SESSION['total']==0){
    header('Location:shop.php?error=Add some items to the cart first');
}

if (isset($_POST['checkout'])) {
    $errorFlag = false;
    $total = 0;
    $total_quantity = 0;

    if (isset($_SESSION['logged_in'])) {
        $query1 = $conn->prepare('SELECT p.id, p.cmimi, p.gjendja, psh.sasi_produkti 
                                  FROM produkte_shporta psh 
                                  JOIN shporta sh ON psh.id_shporte = sh.id 
                                  JOIN produkte p ON psh.id_produkti = p.id 
                                  WHERE sh.id_perdoruesi = ?');
        $query1->bind_param('i', $_SESSION['user_id']);
        $query1->execute();
        $result = $query1->get_result();

        while ($row = $result->fetch_assoc()) {
            $product_id = $row['id'];
            $product_price = $row['cmimi'];
            $available_quantity = $row['gjendja'];
            $cart_quantity = $row['sasi_produkti'];

            if ($cart_quantity > $available_quantity) {
                $errorFlag = true;

                $stmt_update = $conn->prepare('UPDATE produkte_shporta 
                                               SET sasi_produkti = ? 
                                               WHERE id_shporte = (SELECT id FROM shporta WHERE id_perdoruesi = ?) 
                                               AND id_produkti = ?');
                $stmt_update->bind_param('iii', $available_quantity, $_SESSION['user_id'], $product_id);
                $stmt_update->execute();

                echo '<script>alert("The available stock for product ID ' . $product_id . ' has been reduced to ' . $available_quantity . ' units. Your cart has been adjusted.");</script>';
                $cart_quantity = $available_quantity;
            }

            $total += $cart_quantity * $product_price;
            $total_quantity += $cart_quantity;
        }

        $stmt_total = $conn->prepare('UPDATE shporta SET totali = ? WHERE id_perdoruesi = ?');
        $stmt_total->bind_param('ii', $total, $_SESSION['user_id']);
        $stmt_total->execute();

        $_SESSION['total'] = $total;
        $_SESSION['quantity'] = $total_quantity;

    } elseif (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
        foreach ($_SESSION['cart'] as $key => $product) {
            $product_id = $product['product_id'];
            $cart_quantity = $product['product_quantity'];
            $product_price = $product['product_price'];

            $stmt_product = $conn->prepare('SELECT gjendja FROM produkte WHERE id = ?');
            $stmt_product->bind_param('i', $product_id);
            $stmt_product->execute();
            $result_product = $stmt_product->get_result();
            $product_data = $result_product->fetch_assoc();
            $available_quantity = $product_data['gjendja'];

            if ($cart_quantity > $available_quantity) {
                $errorFlag = true;


                $_SESSION['cart'][$product_id]['product_quantity'] = $available_quantity;

                echo '<script>alert("The available stock for product ID ' . $product_id . ' has been reduced to ' . $available_quantity . ' units. Your cart has been adjusted.");</script>';
                $cart_quantity = $available_quantity;
            }


            $total += $cart_quantity * $product_price;
            $total_quantity += $cart_quantity;
        }


        $_SESSION['total'] = $total;
        $_SESSION['quantity'] = $total_quantity;
    }

    if ($errorFlag) {
        echo '<script>alert("Please review your cart, as some items have been adjusted due to stock limitations.");</script>';
        header('Location:cart.php');
    } else {

        header('Location: checkout.php');
        exit;
    }
}
?>


<?php $total = isset($_SESSION['total']) ? $_SESSION['total'] : 0;
 $shipping=0;?>
<?php include 'layouts/header.php'?>
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Check Out</h2>
        <hr class="mx-auto">
    </div>
    <div class="mx-auto container">
        <form id="checkout-form" action="payment.php" method="post" onsubmit="return validateForm()">
            <div class="form-group checkout-small-element">
                <label>Name</label>
                <input type="text" class="form-control" id="checkout-name" name="name" placeholder="Name" required>
            </div>
            <br>
            <div class="form-group checkout-small-element">
                <label>Email</label>
                <input type="email" class="form-control" id="checkout-email" name="email" placeholder="Email" required>
            </div>
            <br>
            <div class="form-group checkout-small-element">
                <label>Phone </label>
                <input type="tel" class="form-control" id="checkout-phone" name="phone" placeholder="phone" required>
            </div>
            <div class="form-group checkout-small-element">
                <label>City</label>
                <input type="text" class="form-control" id="checkout-city" name="city" placeholder="City" required>
            </div>
            <div class="form-group checkout-large-element">
                <label>Country</label>
                <select name="address" class="form-select" id="address" required>
                    <?php
                    include('includes/get_states.php');
                    while ($row = $states->fetch_assoc()) { ?>
                        <option value="<?php echo $row['kosto_shipping']; ?>">
                            <?php echo $row['emri_shtetit']; ?> (Shipping Cost: +<?php echo $row['kosto_shipping'];?>$)
                        </option>
                    <?php } ?>
                </select>
            </div>
            <br>
            <div class="form-group checkout-btn-container">
                <p id="total-amount-text" name="vlera_totale">Total amount: $<?php if (isset($_SESSION['total']))echo number_format($total, 2); ?></p>
                <input type="hidden" id="session-total"  value="<?php echo $total; ?>">
                <input type="submit" class="btn" id="checkout-btn" name="place_order" value="Place Order">
            </div>
        </form>
    </div>
</section>
<script>
    function validateForm() {
        var name = document.getElementById('checkout-name').value;
        var email = document.getElementById('checkout-email').value;
        var phone = document.getElementById('checkout-phone').value;
        var city = document.getElementById('checkout-city').value;
        var address = document.getElementById('checkout-address').value;


        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;


        var phonePattern = /^[0-9]{10}$/;

        if (name === "" || email === "" || phone === "" || city === "" || address === "") {
            alert("All fields must be filled out");
            return false;
        }

        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address");
            return false;
        }

        if (!phonePattern.test(phone)) {
            alert("Please enter a valid phone number (10 digits)");
            return false;
        }

        return true;
    }
    document.getElementById('address').addEventListener('change', function() {
        var shippingCost = parseFloat(this.value); // Get the selected shipping cost
        var sessionTotal = parseFloat(document.getElementById('session-total').value); // Get the session total
        var totalAmount = sessionTotal + shippingCost; // Calculate the new total amount
        document.getElementById('total-amount-text').innerHTML = "Total amount: $" + totalAmount.toFixed(2); // Update the total amount displayed

    });
</script>

