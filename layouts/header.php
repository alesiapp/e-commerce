<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../styling/style.css">


</head>
<body>
<!--Navbar-->
<nav class="navbar navbar-expand-lg bg-white  py-3 fixed-top">
    <div class="container">
        <img class="logo" src="../imgs/Screenshot%202024-09-04%20190459.png"/>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shop.php">Shop</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="cart.php">
                        <i class="fa-solid fa-cart-shopping">
                            <?php if (isset($_SESSION['quantity']) && $_SESSION['quantity']!=0)
                                echo "<span id='number_above'>" .$_SESSION['quantity'] ."</span>"; ?>
                        </i>
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="login.php"><i class="fa-solid fa-user"></i></a>
                </li>



            </ul>
        </div>
    </div>
</nav>
<!--navbar end-->



