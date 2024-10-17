<?php
session_start();
include('includes/connection.php');
if(!isset($_SESSION['logged_in'])&& !isset($_SESSION['total'])){
    header('Location:shop.php');
}

if(isset($_SESSION['logged_in'])){
    if (isset($_POST['add_to_cart'])){
        $stmt1 = $conn->prepare('SELECT id FROM shporta WHERE id_perdoruesi = ?');
        $stmt1->bind_param('i', $_SESSION['user_id']);
        $stmt1->execute();
        $result = $stmt1->get_result();
        $product_id = $_POST['product_id'];
        $product_quantity = $_POST['product_quantity'];

        // Get the available quantity of the product from the database
        $stmt_product = $conn->prepare('SELECT gjendja FROM produkte WHERE id = ?');
        $stmt_product->bind_param('i', $product_id);
        $stmt_product->execute();
        $result_product = $stmt_product->get_result();
        $product_data = $result_product->fetch_assoc();
        $available_quantity = $product_data['gjendja']; // Assuming 'sasi' is the column for available quantity

        // Check if the user requested more quantity than available
        if ($product_quantity > $available_quantity) {
            echo '<script>alert("Sorry, only ' . $available_quantity . ' units are available.");</script>';

        } else {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id_shporta = $row['id'];
                $stmt2 = $conn->prepare('SELECT * FROM produkte_shporta WHERE id_shporte = ? AND id_produkti = ?');
                $stmt2->bind_param('ii', $id_shporta, $product_id);
                $stmt2->execute();
                $result1 = $stmt2->get_result();

                if ($result1->num_rows > 0) {
                    echo '<script>alert("Product was already added to cart");</script>';
                } else {
                    $stmt3 = $conn->prepare('INSERT INTO produkte_shporta (id_shporte, id_produkti, sasi_produkti) VALUES (?,?,?)');
                    $stmt3->bind_param('iii', $id_shporta, $product_id, $product_quantity);
                    $stmt3->execute();
                }
            } else {
                $stmt = $conn->prepare('INSERT INTO shporta (id_perdoruesi) VALUES (?)');
                $stmt->bind_param('i', $_SESSION['user_id']);
                $stmt->execute();
                $id_shportes = $stmt->insert_id;

                $stmt1 = $conn->prepare('INSERT INTO produkte_shporta (id_shporte, id_produkti, sasi_produkti) VALUES (?,?,?)');
                $stmt1->bind_param('iii', $id_shportes, $product_id, $product_quantity);
                $stmt1->execute();
            }
            calculateTotalCart();
        }
    }
    elseif (isset($_POST['remove_product'])){
        $product_id = $_POST['product_id'];
        $stmt4 = $conn->prepare('SELECT id FROM shporta WHERE id_perdoruesi = ?');
        $stmt4->bind_param('i', $_SESSION['user_id']);
        $stmt4->execute();
        $result = $stmt4->get_result();
        $row = $result->fetch_assoc();
        $id_sh = $row['id'];

        $stmt5 = $conn->prepare('DELETE FROM produkte_shporta WHERE id_shporte = ? AND id_produkti = ?');
        $stmt5->bind_param('ii', $id_sh, $product_id);
        $stmt5->execute();
        calculateTotalCart();
    }
    elseif (isset($_POST['edit_quantity'])){
        $product_quantity = $_POST['product_quantity'];
        $product_id = $_POST['product_id'];

        // Get the available quantity of the product from the database
        $stmt_product = $conn->prepare('SELECT gjendja FROM produkte WHERE id = ?');
        $stmt_product->bind_param('i', $product_id);
        $stmt_product->execute();
        $result_product = $stmt_product->get_result();
        $product_data = $result_product->fetch_assoc();
        $available_quantity = $product_data['gjendja']; // Assuming 'sasi' is the column for available quantity

        // Check if the user requested more quantity than available
        if ($product_quantity > $available_quantity) {
            echo '<script>alert("Sorry, only ' . $available_quantity . ' units are available.");</script>';
        } else {
            $stmt6 = $conn->prepare('SELECT id FROM shporta WHERE id_perdoruesi = ?');
            $stmt6->bind_param('i', $_SESSION['user_id']);
            $stmt6->execute();
            $result = $stmt6->get_result();
            $row = $result->fetch_assoc();
            $id_sh = $row['id'];

            $stmt7 = $conn->prepare('UPDATE produkte_shporta SET sasi_produkti = ? WHERE id_shporte = ? AND id_produkti = ?');
            $stmt7->bind_param('iii', $product_quantity, $id_sh, $product_id);
            $stmt7->execute();
            calculateTotalCart();
        }
    }
}
else if(!isset($_SESSION['logged_in'])){
    if (isset($_POST['add_to_cart'])) {
        // Fetch available quantity from the database
        $product_id = $_POST['product_id'];
        $product_quantity = $_POST['product_quantity'];

        $stmt_product = $conn->prepare('SELECT gjendja FROM produkte WHERE id = ?');
        $stmt_product->bind_param('i', $product_id);
        $stmt_product->execute();
        $result_product = $stmt_product->get_result();
        $product_data = $result_product->fetch_assoc();
        $available_quantity = $product_data['gjendja'];

        if ($product_quantity > $available_quantity) {
            echo '<script>alert("Sorry, only ' . $available_quantity . ' units are available.");</script>';
        } else {
            // Check if the cart session is set
            if (isset($_SESSION['cart'])) {
                // Get array of product IDs already in the cart
                $product_array_ids = array_column($_SESSION['cart'], 'product_id');

                // Check if the product is already in the cart
                if (!in_array($_POST['product_id'], $product_array_ids)) {
                    $product_array = array(
                        'product_id' => $_POST['product_id'],
                        'product_price' => $_POST['product_price'],
                        'product_image' => $_POST['product_image'],
                        'product_quantity' => $_POST['product_quantity'],
                        'product_name' => $_POST['product_name']
                    );

                    // Add the product to the cart session
                    $_SESSION['cart'][$_POST['product_id']] = $product_array;
                } else {
                    echo '<script>alert("Product was already added to cart");</script>';
                }
            } else {
                // If cart session doesn't exist, create the cart with the first product
                $product_id = $_POST['product_id'];
                $product_price = $_POST['product_price'];
                $product_name = $_POST['product_name'];
                $product_quantity = $_POST['product_quantity'];
                $product_image = $_POST['product_image'];

                $product_array = array(
                    'product_id' => $product_id,
                    'product_price' => $product_price,
                    'product_image' => $product_image,
                    'product_quantity' => $product_quantity,
                    'product_name' => $product_name
                );
                $_SESSION['cart'][$product_id] = $product_array;
            }

            // Call function to calculate the total cart value
            calculateTotalCart1();
        }
    }

    elseif (isset($_POST['remove_product'])){
        unset($_SESSION['cart'][$_POST['product_id']]);
        calculateTotalCart1();
    }
    elseif (isset($_POST['edit_quantity'])) {
        // Get the new quantity from the form
        $new_quantity = $_POST['product_quantity'];
        $product_id = $_POST['product_id'];

        // Fetch available quantity from the database
        $stmt_product = $conn->prepare('SELECT gjendja FROM produkte WHERE id = ?');
        $stmt_product->bind_param('i', $product_id);
        $stmt_product->execute();
        $result_product = $stmt_product->get_result();
        $product_data = $result_product->fetch_assoc();
        $available_quantity = $product_data['gjendja'];

        // Check if the new quantity exceeds the available stock
        if ($new_quantity > $available_quantity) {
            echo '<script>alert("Sorry, only ' . $available_quantity . ' units are available.");</script>';
        } else {
            // Update the quantity in the session cart
            $product_array = $_SESSION['cart'][$product_id];
            $product_array['product_quantity'] = $new_quantity;
            $_SESSION['cart'][$product_id] = $product_array;

            // Recalculate the total cart value
            calculateTotalCart1();
        }
    }

}


function calculateTotalCart() {
    global $conn;
    $total = 0;
    $total_quantity = 0;
    $query2 = $conn->prepare('SELECT cmimi, sasi_produkti FROM produkte_shporta psh 
                              JOIN shporta sh ON psh.id_shporte = sh.id 
                              JOIN produkte p ON psh.id_produkti = p.id 
                              WHERE id_perdoruesi = ?');
    $query2->bind_param('i', $_SESSION['user_id']);
    $query2->execute();
    $rezultati = $query2->get_result();
    while ($row = $rezultati->fetch_assoc()) {
        $total_quantity += $row['sasi_produkti'];
        $total += $row['sasi_produkti'] * $row['cmimi'];
    }
    $query3 = $conn->prepare('UPDATE shporta SET totali = ? WHERE id_perdoruesi = ?');
    $query3->bind_param('ii', $total, $_SESSION['user_id']);
    $query3->execute();
    $_SESSION['total'] = $total;
    $_SESSION['quantity'] = $total_quantity;
}

// Function to calculate total cart for guests
function calculateTotalCart1() {
    $total = 0;
    $total_quantity = 0;

    foreach ($_SESSION['cart'] as $key => $value) {
        $price = $value['product_price'];
        $quantity = $value['product_quantity'];
        $total += $price * $quantity;
        $total_quantity += $quantity;
    }

    $_SESSION['total'] = $total;
    $_SESSION['quantity'] = $total_quantity;
}
?>

<!-- HTML Section -->
<?php include('layouts/header.php');?>
<?php if (isset($_SESSION['logged_in'])){ ?>
<section class="cart container my-5 py-5">
    <div class="container mt-5">
        <h2 class="font-weight-bold">Your Cart</h2>
        <hr>
    </div>

    <table class="mt-5 pt-5">
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>
        <?php
        $query1=$conn->prepare('Select foto1,emri,cmimi,p.id,sasi_produkti ,gjendja from produkte_shporta psh join shporta sh on id_shporte=id 
    join produkte p on psh.id_produkti=p.id where id_perdoruesi=?');
        $query1->bind_param('i',$_SESSION['user_id']);
        $query1->execute();
        $produkte_shporte=$query1->get_result();
        while ($value=$produkte_shporte->fetch_assoc()){

            ?>
            <tr>
                <td>
                    <div class="product-info">
                        <img src="imgs/<?php echo $value['foto1'];?>">
                        <div>
                            <p><?php echo $value['emri']?></p>
                            <small><span>$</span><?php echo $value['cmimi'];?></small>
                            <br>
                            <form method="post" action="cart.php"  >
                                <input type="hidden" name="product_id" value="<?php echo $value['id'];?>">
                                <input type="submit" class="remove-btn" name="remove_product"
                                       value="remove">

                            </form>

                        </div>
                    </div>
                </td>
                <td>

                    <form method="post" action="cart.php">
                        <input type="submit" class="edit-btn" value="edit"  name="edit_quantity">
                        <input type="hidden"  name="product_id" value="<?php echo $value['id'];?>">
                        <input type="number" name="product_quantity" value="<?php echo $value['sasi_produkti'];?>" max="<?php echo $value['gjendja'];?>" min="1">
                    </form>

                </td>
                <td>
                    <span>$</span>
                    <span class="product-price"><?php echo $value['cmimi']*$value['sasi_produkti'];?></span>
                </td>
            </tr>
        <?php } ?>
    </table>

    <div class="cart-total">
        <table>

            <tr>
                <td>Total</td>
                <td>$<?php if (isset($_SESSION['total'])) echo $_SESSION['total'];
                    ?></td>
            </tr>
        </table>
    </div>

    <div class="checkout-container">
        <form method="post" action="checkout.php">
            <input type="submit" class="btn checkout-btn" value="Checkout" name="checkout">
        </form>
    </div>
</section>
<?php } elseif (isset($_SESSION['cart'])){ ?>
    <section class="cart container my-5 py-5">
        <div class="container mt-5">
            <h2 class="font-weight-bold">Your Cart</h2>
            <hr>


        </div>
        <table class="mt-5 pt-5">
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
            <?php if(isset($_SESSION['cart']))foreach ($_SESSION['cart'] as $key=>$value){ ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="imgs/<?php echo $value['product_image'];?>">
                            <div>
                                <p><?php echo $value['product_name']?></p>
                                <small><span>$</span><?php echo $value['product_price'];?></small>
                                <br>
                                <form method="post" action="cart.php"  >
                                    <input type="hidden" name="product_id" value="<?php echo $value['product_id'];?>">
                                    <input type="submit" class="remove-btn" name="remove_product"
                                           value="remove">

                                </form>

                            </div>
                        </div>
                    </td>
                    <td>

                        <form method="post" action="cart.php">
                            <input type="submit" class="edit-btn" value="edit" name="edit_quantity">
                            <input type="hidden"  name="product_id" value="<?php echo $value['product_id'];?>">
                            <input type="number" name="product_quantity" value="<?php echo $value['product_quantity'];?>">
                        </form>

                    </td>
                    <td>
                        <span>$</span>
                        <span class="product-price"><?php echo $value['product_price']*$value['product_quantity'];?></span>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <div class="cart-total">
            <table>

                <tr>
                    <td>Total</td>
                    <td>$<?php  if(isset($_SESSION['cart'])) echo $_SESSION['total']; ?></td>
                </tr>
            </table>
        </div>

        <div class="checkout-container">
            <form method="post" action="checkout.php">
                <input type="submit" class="btn checkout-btn" value="Checkout" name="checkout">
            </form>
        </div>
    </section>
<?php }
include('layouts/footer.php');
?>
