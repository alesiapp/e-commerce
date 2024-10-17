<?php

include ('layouts/header.php');
include('includes/connection.php');
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
if(!isset($_SESSION['logged_in'])){
    header('location:login.php?error=Login first');
    exit;
}
if(isset($_GET['logout'])){
    if(isset($_SESSION['logged_in'])){
        unset($_SESSION['logged_in']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_id']);
        unset($_SESSION['total']);
        unset($_SESSION['quantity']);
        header('location:login.php');
        exit;
    }
}


if(isset($_SESSION['logged_in'])){

    $stmt = $conn->prepare('Select * from porosi where id_perdoruesi=?');
    $stmt->bind_param('i',$_SESSION['user_id']);

    $stmt->execute();
    $orders= $stmt->get_result();

    $stmt2 = $conn->prepare('SELECT statusi FROM perdorues WHERE id = ?');
    $stmt2->bind_param('i', $_SESSION['user_id']);
    $stmt2->execute();
    $stmt2->bind_result($status);
    $stmt2->fetch();


}
?>



<!--Account-->
    <section class="my-5 py-5 d-flex justify-content-center align-items-center">
        <div class="container mx-auto text-center">
            <?php if (isset($_GET['payment_message'])) { ?>
                <p class="mt-5 text-center" style="color: green;"><?php echo $_GET['payment_message']; ?></p>
            <?php } ?>
            <?php if (isset($_GET['success'])) { ?>
                <p class="mt-5 text-center" style="color: green;"><?php echo $_GET['success']; ?></p>
            <?php } ?>
            <?php if (isset($_GET['error'])) { ?>
                <p class="mt-5 text-center" style="color: red;"><?php echo $_GET['error']; ?></p>
            <?php } ?>


            <div class="text-center mt-3 pt-5 col-lg-6 col-md-8 col-sm-12 mx-auto">

                <h3 class="font-weight-bold">Account Info</h3>
                <hr class="mx-auto" style="width: 50%;">
                <div class="account-info">
                    <p>Name : <span><?php echo $_SESSION['user_name']; ?></span></p>
                    <p>Email :  <span><?php echo $_SESSION['user_email']; ?></span></p>
                    <p><a href="#orders" id="orders-btn">Your Orders</a></p>
                    <?php if($status==0){

                        ?>

                    <p><a href="send_otp.php?verify=1">Verify Account</a></p>
                    <?php }?>
                    <p><a href="account.php?logout=1" id="logout-btn">Logout</a></p>
                    <p><a href="edit_account.php" id="edit-account-btn">Change Password</a></p>
                </div>
            </div>
        </div>
    </section>


<!--Orders-->
    <section id="orders" class="orders container my-5 py-3">
        <div class="container mt-2">
            <h2 class="font-weight-bold text-center">Your Orders</h2>
            <hr class="mx-auto">
        </div>

        <!-- Add a responsive table wrapper -->
        <div class="table-responsive">
            <table class="table mt-5 pt-5">
                <thead>
                <tr>
                    <th>Order Id</th>
                    <th>Order Cost</th>
                    <th>Order Status</th>
                    <th>Order Date</th>
                    <th id="order-details">Order Details</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row=$orders->fetch_assoc()) {?>
                    <tr>
                        <td><span><?php echo $row['id']?></span></td>
                        <td><span><?php echo $row['kosto']?></span></td>
                        <td><span><?php echo $row['statusi']?></span></td>
                        <td><span><?php echo $row['data']?></span></td>
                        <td>
                            <form method="post" action="order_details.php">
                                <input type="hidden" value="<?php echo $row['statusi'];?>" name="order_status">
                                <input type="hidden" name="order_id" value="<?php echo $row['id'];?>">
                                <input class="btn order-details-btn" name="order_details" type="submit" value="Details">
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div> <!-- End table-responsive -->
    </section>


<!--Footer-->

<?php
include ('layouts/footer.php');
?>