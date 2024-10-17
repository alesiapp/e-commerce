<?php
session_start();
include('connection.php');


if (isset($_POST['transaction_id'])) {
    $transaction_id = $_POST['transaction_id'];
    $kosto = $_SESSION['total'] + $_SESSION['shipping'];

    // PayPal API credentials
    $clientId = "AYYQWdLvZJjPnkURg_kqUj5m3fqJ2pGmh-yMoCNzac-OfMAT8GDZrjWxvJlgQUXvix7-Mip1DnkwVDqL";
    $clientSecret = "EA1oO-dCX1DTs33ztmQO4DnB0DOuDm0jnYYAHCa8nShLQ5hhArncyC2n3VE5cUdGv4_Fsxpew5T0bUcA";

    // Set the correct PayPal API URL
    $paypalUrl = "https://api-m.sandbox.paypal.com/v2/checkout/orders/$transaction_id";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $paypalUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($clientId . ":" . $clientSecret)
    ]);

    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        $_SESSION['reason']=true;
        header('Location:restore_stock.php');
        die('Error: ' . curl_error($ch));
    }

    curl_close($ch);

    $result = json_decode($response, true);


    if (isset($result['status']) && $result['status'] === 'COMPLETED' &&
        $result['purchase_units'][0]['amount']['value'] == number_format($kosto, 2)) {


        $payment_date = date('Y-m-d H:i:s');
        $order_status = 'paid';

        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        } else {
            $user_id = NULL;
        }

        $stmt1 = $conn->prepare('INSERT INTO porosi (statusi, kosto, id_perdoruesi, nr_tel, qyteti, shteti) 
                                 VALUES (?, ?, ?, ?, ?, ?)');
        $stmt1->bind_param('siissi', $order_status, $kosto, $user_id, $_SESSION['phone'], $_SESSION['city'], $_SESSION['address']);
        $stmt1->execute();
        $order_id = $stmt1->insert_id;

        $stmt2 = $conn->prepare('INSERT INTO pagesa (id_porosi, id_user, transaksion_id, data) 
                                 VALUES (?, ?, ?, ?)');
        $stmt2->bind_param('iiss', $order_id, $user_id, $transaction_id, $payment_date);
        $stmt2->execute();


        if (isset($_SESSION['user_id'])) {
            $query1 = $conn->prepare('SELECT foto1, emri, cmimi, p.id, sasi_produkti 
                                      FROM produkte_shporta psh 
                                      JOIN shporta sh ON psh.id_shporte = sh.id 
                                      JOIN produkte p ON psh.id_produkti = p.id 
                                      WHERE sh.id_perdoruesi = ?');
            $query1->bind_param('i', $_SESSION['user_id']);
            $query1->execute();
            $produkte_shporte = $query1->get_result();

            while ($value = $produkte_shporte->fetch_assoc()) {
                $stmt1 = $conn->prepare('INSERT INTO produkte_porosi (id_porosi, id_produkti, sasi_produkti) 
                                         VALUES (?, ?, ?)');
                $stmt1->bind_param('iii', $order_id, $value['id'], $value['sasi_produkti']);
                $stmt1->execute();
            }
            $stmt4 = $conn->prepare('SELECT id FROM shporta WHERE id_perdoruesi = ?');
            $stmt4->bind_param('i', $_SESSION['user_id']);
            $stmt4->execute();
            $result = $stmt4->get_result();
            $row = $result->fetch_assoc();
            $id_sh = $row['id'];
            $stmt5 = $conn->prepare('DELETE FROM produkte_shporta WHERE id_shporte = ?');
            $stmt5->bind_param('i', $id_sh);
            $stmt5->execute();
            $stmt6 = $conn->prepare('DELETE FROM shporta WHERE id = ?');
            $stmt6->bind_param('i', $id_sh);
            $stmt6->execute();
        }

        elseif (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_id => $product) {
                $cart_quantity = $product['product_quantity'];

                // Insert into `produkte_porosi` (order products)
                $stmt1 = $conn->prepare('INSERT INTO produkte_porosi (id_porosi, id_produkti, sasi_produkti) 
                                         VALUES (?, ?, ?)');
                $stmt1->bind_param('iii', $order_id, $product_id, $cart_quantity);
                $stmt1->execute();
            }

            // Clear the guest's session cart after order completion
            unset($_SESSION['cart']);
        }

        // Clear session variables related to the order process
        unset($_SESSION['address']);
        unset($_SESSION['city']);
        unset($_SESSION['phone']);
        unset($_SESSION['total']);
        unset($_SESSION['quantity']);


        echo json_encode(['status' => 'success', 'redirect' => 'shop.php?payment_message=Paid successfully, thanks for shopping with us']);
        exit();
    } else {

        echo json_encode(['status' => 'error', 'redirect' => 'shop.php?payment_message=Payment failed or amount mismatch.']);
        exit();
    }
} else {

    echo json_encode(['status' => 'error', 'redirect' => '../index.php']);
    exit();
}
?>
