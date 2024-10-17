<?php
include('includes/connection.php');
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);
    die('Invalid request method');
}

$conn->begin_transaction();

try {
    $stock_sufficient = true;
    $insufficient_stock_message = '';


    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
        $user_id = $_SESSION['user_id'];
        $query = $conn->prepare('SELECT p.id, p.gjendja, ps.sasi_produkti 
                                 FROM produkte_shporta ps 
                                 JOIN produkte p ON ps.id_produkti = p.id 
                                 JOIN shporta s ON ps.id_shporte = s.id 
                                 WHERE s.id_perdoruesi = ? for update ');
        $query->bind_param('i', $user_id);
        $query->execute();
        $cart_products = $query->get_result();

        if ($cart_products->num_rows > 0) {
            while ($product = $cart_products->fetch_assoc()) {
                $product_id = $product['id'];
                $cart_quantity = $product['sasi_produkti'];
                $available_stock = $product['gjendja'];

                if ($cart_quantity > $available_stock) {
                    $stock_sufficient = false;
                    $_SESSION['nostock'] = true;
                    $insufficient_stock_message .= "Insufficient stock for product ID: $product_id. Available: $available_stock, Requested: $cart_quantity.<br>";
                    break;
                }
            }

            if ($stock_sufficient) {
                $cart_products->data_seek(0);
                while ($product = $cart_products->fetch_assoc()) {
                    $product_id = $product['id'];
                    $cart_quantity = $product['sasi_produkti'];

                    $update_stock = $conn->prepare('UPDATE produkte SET gjendja = gjendja - ? WHERE id = ?');
                    $update_stock->bind_param('ii', $cart_quantity, $product_id);
                    $update_stock->execute();
                }
            } else {
                throw new Exception($insufficient_stock_message);
            }
        } else {
            throw new Exception("No products in the cart for the logged-in user.");
        }
    }
   elseif (isset($_SESSION['cart']) && count($_SESSION['cart']) != 0) {
    // Handle guest users
    $stock_sufficient = true;
    $insufficient_stock_message = '';

    foreach ($_SESSION['cart'] as $product_id => $cart_item) {

        $cart_quantity = $cart_item['product_quantity'];
        $query = $conn->prepare('SELECT gjendja FROM produkte WHERE id = ?');
        $query->bind_param('i', $product_id);
        $query->execute();
        $query->bind_result($available_stock);
        $query->fetch();
        $query->close();

        if ($cart_quantity > $available_stock) {
            $stock_sufficient = false;
            $_SESSION['nostock'] = true;
            $insufficient_stock_message .= "Insufficient stock for product ID: $product_id. Available: $available_stock, Requested: $cart_quantity.<br>";
            break;
        }
    }

    if ($stock_sufficient) {

        foreach ($_SESSION['cart'] as $product_id => $cart_item) {
            $cart_quantity = $cart_item['product_quantity'];

            $update_stock = $conn->prepare('UPDATE produkte SET gjendja = gjendja - ? WHERE id = ?');
            $update_stock->bind_param('ii', $cart_quantity, $product_id);
            $update_stock->execute();
            $update_stock->close();
        }
    } else {
        throw new Exception($insufficient_stock_message);
    }
    } else {
        throw new Exception("No products in the cart.");
    }

    $conn->commit();
    echo "Stock reserved successfully.";

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['nostock']=true;
    error_log("Error reserving stock: " . $e->getMessage());
    echo "Failed to reserve stock. Try again.";
}
?>
