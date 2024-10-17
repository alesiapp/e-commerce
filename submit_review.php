<?php
session_start();
include("includes/connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review_btn'])) {
    $order_id = $_POST['order_id'];
    $reviews =htmlspecialchars( $_POST['reviews']);
    $ratings =htmlspecialchars($_POST['ratings']) ;
    $user_id = $_SESSION['user_id'];
    foreach ($reviews as $product_id => $review) {
        $rating = isset($ratings[$product_id]) ? $ratings[$product_id] : null;
        if (!empty($review) && !empty($rating)) {
            $stmt = $conn->prepare("INSERT INTO reviews (id_perdoruesi, id_produkti, 
                                           id_porosi, pershkrimi, vleresimi, data) 
                                            VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param('iiisi', $user_id, $product_id, $order_id, $review, $rating);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = "Error adding the review. Please try again.";
            }
            $stmt->close();
        }
    }

    if (isset($success)) {
        header("Location: review.php");
        exit();
    } else {
        header("Location: review.php?order_id=$order_id&error=$error");
        exit();
    }
} else {
    header("Location: review.php");
    exit();
}
