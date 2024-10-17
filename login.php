<?php
session_start();
include('layouts/header.php');
include('includes/connection.php');

if (isset($_SESSION['logged_in'])) {
    header('location:account.php');
    exit;
}

if (isset($_POST['login_button'])) {
    $email = filter_var(trim($_POST['l-email']), FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: login.php?error=' . urlencode('Invalid email address'));
        exit();
    }
    $password = htmlspecialchars(trim($_POST['l-password']));

    $stmt = $conn->prepare('SELECT id, emri, email, password FROM perdorues WHERE email = ?');
    $stmt->bind_param('s', $email);

    if ($stmt->execute()) {
        $stmt->bind_result($user_id, $username, $user_email, $user_password);
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->fetch();


            if (password_verify($password, $user_password)) {

                if (isset($_SESSION['cart'])) {
                    // Retrieve the session cart
                    $session_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

                    // Fetch the user's database cart
                    $stmt2 = $conn->prepare('SELECT id FROM shporta WHERE id_perdoruesi = ?');
                    $stmt2->bind_param('i', $user_id);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();

                    if ($result2->num_rows == 0) {

                        $stmt3 = $conn->prepare('INSERT INTO shporta (id_perdoruesi, totali) VALUES (?, 0)');
                        $stmt3->bind_param('i', $user_id);
                        $stmt3->execute();
                        $cart_id = $stmt3->insert_id;
                    } else {

                        $row = $result2->fetch_assoc();
                        $cart_id = $row['id'];
                    }


                    foreach ($session_cart as $product_id => $cart_item) {
                        $cart_quantity = $cart_item['product_quantity'];

                        $stmt4 = $conn->prepare('SELECT sasi_produkti FROM produkte_shporta WHERE id_shporte = ? AND id_produkti = ?');
                        $stmt4->bind_param('ii', $cart_id, $product_id);
                        $stmt4->execute();
                        $result4 = $stmt4->get_result();

                        if ($result4->num_rows > 0) {

                            $row = $result4->fetch_assoc();
                            $new_quantity = $row['sasi_produkti'] + $cart_quantity;

                            $stmt5 = $conn->prepare('UPDATE produkte_shporta SET sasi_produkti = ? WHERE id_shporte = ? AND id_produkti = ?');
                            $stmt5->bind_param('iii', $new_quantity, $cart_id, $product_id);
                            $stmt5->execute();
                        } else {

                            $stmt6 = $conn->prepare('INSERT INTO produkte_shporta (id_shporte, id_produkti, sasi_produkti) VALUES (?, ?, ?)');
                            $stmt6->bind_param('iii', $cart_id, $product_id, $cart_quantity);
                            $stmt6->execute();
                        }
                    }


                    $stmt7 = $conn->prepare('SELECT SUM(p.cmimi * ps.sasi_produkti) AS total 
                                             FROM produkte p 
                                             JOIN produkte_shporta ps ON p.id = ps.id_produkti 
                                             WHERE ps.id_shporte = ?');
                    $stmt7->bind_param('i', $cart_id);
                    $stmt7->execute();
                    $result7 = $stmt7->get_result();

                    if ($result7->num_rows > 0) {
                        $row = $result7->fetch_assoc();
                        $new_total = $row['total'];

                        // Update the total in the shporta table
                        $stmt8 = $conn->prepare('UPDATE shporta SET totali = ? WHERE id = ?');
                        $stmt8->bind_param('di', $new_total, $cart_id);
                        $stmt8->execute();

                        // Set session total for the user
                        $_SESSION['total'] = $new_total;
                    }

                    // Clear session cart after merging
                    unset($_SESSION['quantity']);
                    unset($_SESSION['cart']);
                }

                // Set session for logged-in user
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $username;
                $_SESSION['user_email'] = $user_email;
                $_SESSION['logged_in'] = true;

                // Fetch the total for the user's cart
                $stmt9 = $conn->prepare('SELECT totali FROM shporta WHERE id_perdoruesi = ?');
                $stmt9->bind_param('i', $user_id);
                $stmt9->execute();
                $result9 = $stmt9->get_result();

                if ($result9->num_rows > 0) {
                    $row = $result9->fetch_assoc();
                    $totali = $row['totali'];
                    $_SESSION['total'] = $totali;
                }

                header('location:account.php?success='. urlencode('Logged in successfully'));
            } else {
                header('location:login.php?error='. urlencode('Invalid email or password'));
            }
        } else {
            header('location:login.php?error='.urlencode('Invalid email or password'));
        }
    } else {
        header('location:login.php?error='.urlencode('Something went wrong'));
    }
}

?>

<!-- Login HTML -->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Login</h2>
        <hr class="mx-auto">

        <div class="mx-auto container">
            <form id="login-form" action="login.php" method="post" onsubmit="return validateLoginForm()">
                <p style="color: red" class="text-center"><?php if (isset($_GET['error'])) echo $_GET['error']; ?></p>
                <p style="color: red" class="text-center"><?php if (isset($_GET['cart'])) echo $_GET['cart']; ?></p>
                <p style="color: red" class="text-center"><?php if (isset($_GET['success'])) echo $_GET['success']; ?></p>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" id="login-email" name="l-email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="login-password" name="l-password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="submit" name="login_button" class="btn" id="login-button" value="Login">
                </div>
                <div class="form-group">
                    <a id="register-url" href="register.php" class="btn">Do not have an account? Register</a>
                </div>
                <div class="form-group">
                    <a id="register-url" href="reset.php" class="btn">Forgot your password?</a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    document.getElementById("login-email").addEventListener("input", validateLoginForm);
    document.getElementById("login-password").addEventListener("input", validateLoginForm);

    function validateLoginForm() {
        var email = document.getElementById("login-email").value;
        var password = document.getElementById("login-password").value;
        var errorMessage = document.getElementById("error-message");

        // Reset error message
        errorMessage.innerHTML = "";

        // Check if fields are empty
        if (email === "") {
            errorMessage.innerHTML = "Email cannot be empty.";
            return false;
        }

        if (password === "") {
            errorMessage.innerHTML = "Password cannot be empty.";
            return false;
        }

        // Basic email format validation
        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailPattern.test(email)) {
            errorMessage.innerHTML = "Please enter a valid email address.";
            return false;
        }

        return true;
    }

    // Prevent form submission if validation fails
    document.getElementById("login-form").addEventListener("submit", function(event) {
        if (!validateLoginForm()) {
            event.preventDefault(); // Prevent form submission
        }
    });
</script>

<!-- Footer -->

<?php include('layouts/footer.php'); ?>
