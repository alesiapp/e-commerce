<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include('layouts/header.php');
include('includes/connection.php');

require 'vendor/autoload.php';

if (isset($_POST['register'])) {
    $name = htmlspecialchars(trim($_POST['r-name']));
    $email =htmlspecialchars(trim($_POST['r-email'])) ;
    $password = htmlspecialchars(trim($_POST['r-password']));
    $confirmpassword =htmlspecialchars(trim($_POST['r-confirmpassword'])) ;


    if ($name === "" || $email === "" || $password === "" || $confirmpassword === "") {
        header('Location: register.php?error='. urlencode('Empty fields'));
        exit();
    }

//    if ($confirmpassword !== $password) {
//        header('Location: register.php?error='. urlencode('Passwords do not match'));
//        exit();
//    }

    if (strlen($password) < 6) {
        header('Location: register.php?error='. urlencode('Password should be at least 6 characters'));
        exit();
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: register.php?error=' . urlencode('Invalid email address'));
        exit();
    }


    $stmt2 = $conn->prepare('SELECT 1 FROM perdorues WHERE email = ? LIMIT 1');
    $stmt2->bind_param('s', $email);
    $stmt2->execute();
    $stmt2->store_result();
    if ($stmt2->num_rows > 0) {
        header('Location: register.php?error=' . urlencode('This email address has already been used'));
        exit();
    } else {
        $n = 0;
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt1 = $conn->prepare('INSERT INTO perdorues (emri, email, password, statusi) VALUES (?, ?, ?, ?)');
        $stmt1->bind_param('sssi', $name, $email, $hashed_password, $n);
        if ($stmt1->execute()) {
            $user_id = $stmt1->insert_id;

            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $name;
            $_SESSION['logged_in'] = true;

            // Add guest cart items to database if user was a guest
            if (isset($_SESSION['cart'])&& count($_SESSION['cart'])!=0) {
                $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
                $query=$conn->prepare('Insert into shporta(id_perdoruesi,totali) values (?,?)');
                $query->bind_param('ii',$user_id,$_SESSION['total']);
                $query->execute();
                $sh_id=$query->insert_id;

                foreach ($cart as $product_id => $product_details) {
                    $quantity = $product_details['product_quantity'];




                    $stmt3 = $conn->prepare('INSERT INTO produkte_shporta (id_shporte, id_produkti, sasi_produkti) VALUES (?, ?, ?)');
                    $stmt3->bind_param('iii', $sh_id, $product_id, $quantity);
                    $stmt3->execute();
                }

                unset($_SESSION['cart']);


            }

            $otp = random_int(100000, 999999);
            $_SESSION['otp'] = $otp;

            // Send OTP via email
            require "vendor/phpmailer/phpmailer/src/PHPMailer.php";
            $mail = new PHPMailer;

            $mail->isSMTP();
            $mail->Host = 'smtp.outlook.com';
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';

            $mail->Username = '';
            $mail->Password = '';

            $mail->setFrom('ecommercealesia@outlook.com', 'E-commerce');
            $mail->addAddress($_POST["r-email"]);

            $mail->isHTML(true);
            $mail->Subject = "Your verify code";
            $mail->Body = "<p>Dear user, </p> <h3>Your verify OTP code is $otp <br></h3><br><br><p>With regards,</p><b></b>";

            if (!$mail->send()) {
                echo "<script>alert('Verification Failed, Invalid Email');</script>";
            } else {
                $_SESSION['verify'] = 1;
                echo "<script>alert('Register Successfully, OTP sent to $email'); window.location.replace('verification.php');</script>";
            }
        } else {
            header('Location: register.php?error='. urlencode('Could not create account'));
            exit();
        }
    }
}
?>

<!-- Register -->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Register</h2>
        <hr class="mx-auto">

        <div class="mx-auto container">
            <form id="register-form" method="post" action="register.php" onsubmit="return validateRegisterForm()">
                <p style="color: red"><?php if (isset($_GET['error'])) { echo $_GET['error']; } ?></p>
                <p id="error-message" style="color: red" class="text-center"></p>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="register-name" name="r-name" placeholder="Name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" id="register-email" name="r-email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="register-password" name="r-password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" class="form-control" id="register-confirm-password" name="r-confirmpassword" placeholder="Confirm Password" required>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn" id="register-button" name="register" value="Register">
                </div>
                <div class="form-group">
                    <a id="login-url" href="login.php" class="btn">Do you have an account? Login</a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    function validateRegisterForm() {
        var name = document.getElementById("register-name").value;
        var email = document.getElementById("register-email").value;
        var password = document.getElementById("register-password").value;
        var confirmPassword = document.getElementById("register-confirm-password").value;
        var errorMessage = document.getElementById("error-message");

        errorMessage.innerHTML = "";

        if (name === "") {
            errorMessage.innerHTML = "Name is required.";
            return false;
        }

        if (email === "") {
            errorMessage.innerHTML = "Email is required.";
            return false;
        }

        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailPattern.test(email)) {
            errorMessage.innerHTML = "Please enter a valid email address.";
            return false;
        }

        if (password === "") {
            errorMessage.innerHTML = "Password is required.";
            return false;
        }

        if (password.length < 6) {
            errorMessage.innerHTML = "Password must be at least 6 characters long.";
            return false;
        }

        if (confirmPassword.trim() === "") {
            errorMessage.innerHTML = "Confirm Password is required.";
            return false;
        }

        if (password !== confirmPassword) {
            errorMessage.innerHTML = "Passwords do not match.";
            return false;
        }

        return true;
    }
</script>

<!-- Footer -->
<?php include('layouts/footer.php'); ?>
