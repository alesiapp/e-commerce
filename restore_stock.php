<?php
include('includes/connection.php');
session_start();

if ((!isset($_SESSION['user_id']) && !isset($_SESSION['cart'])) || (!isset($_POST['reason'])&& !isset($_SESSION['reason']))) {
    header('Location: cart.php');
    exit();
}
if(isset($_SESSION['nostock'])){

    unset($_SESSION['nostock']);
    header('Location:cart.php');
    exit();
}


$conn->begin_transaction();

try {

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $query = $conn->prepare('SELECT p.id, ps.sasi_produkti 
                                 FROM produkte_shporta ps 
                                 JOIN produkte p ON ps.id_produkti = p.id 
                                 JOIN shporta s ON ps.id_shporte = s.id 
                                 WHERE s.id_perdoruesi = ?');
        $query->bind_param('i', $user_id);
        $query->execute();
        $cart_products = $query->get_result();
        while ($product = $cart_products->fetch_assoc()) {
            $product_id = $product['id'];
            $cart_quantity = $product['sasi_produkti'];
            $restore_stock = $conn->prepare('UPDATE produkte SET gjendja = gjendja + ? WHERE id = ?');
            $restore_stock->bind_param('ii', $cart_quantity, $product_id);
            $restore_stock->execute();
        }
    }


    elseif (isset($_SESSION['cart'])&& count($_SESSION['cart']) > 0) {
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {

            foreach ($_SESSION['cart'] as $product_id => $product) {
                $cart_quantity = $product['product_quantity'];


                $restore_stock = $conn->prepare('UPDATE produkte SET gjendja = gjendja + ? WHERE id = ?');
                $restore_stock->bind_param('ii', $cart_quantity, $product_id);
                $restore_stock->execute();
            }
        }
    }


    $conn->commit();
    header('Location:cart.php');
    exit();

} catch (Exception $e) {
    $conn->rollback();
    echo "Error restoring stock: " . $e->getMessage();
}
?>
