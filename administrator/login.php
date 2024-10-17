<?php
session_start();
include('../includes/connection.php');


session_start();
include '../includes/connection.php';

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

if (isset($_POST['login_button'])) {

    $email = filter_var(trim($_POST['l-email']), FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Invalid email format
        header('Location: login.php?error=' . urlencode('Invalid email address'));
        exit();
    }
    $password = trim($_POST['l-password']);

    if (empty($email) || empty($password)) {
        header('Location: login.php?error=' . urlencode('Empty Fields'));
        exit;
    }

    $stmt = $conn->prepare('SELECT admin_id, admin_name, admin_email, admin_password FROM administrator WHERE admin_email = ?');
    $stmt->bind_param('s', $email);
    if ($stmt->execute()) {
        $stmt->bind_result($admin_id, $name, $admin_email, $admin_password);
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->fetch();
            if (password_verify($password, $admin_password)) {
                $_SESSION['admin_id'] = $admin_id;
                $_SESSION['admin_name'] = $name;
                $_SESSION['admin_email'] = $admin_email;
                $_SESSION['admin_logged_in'] = true;

                header('Location: index.php?success=' . urlencode('Logged in successfully'));
                exit;
            } else {
                header('Location: login.php?error=' . urlencode('Invalid email or password'));
                exit;
            }
        } else {
            header('Location: login.php?error=' . urlencode('Could not verify account'));
            exit;
        }
    } else {
        header('Location: login.php?error=' . urlencode('Something went wrong'));
        exit;
    }

    $stmt->close();
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<!-- Login Section -->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="font-weight-bold">Admin Login</h2>
        <hr class="mx-auto" style="width: 50%;">
        <div class="mx-auto container" style="max-width: 500px;">
            <form id="login-form" action="login.php" method="post" onsubmit="return validateLoginForm()">
                <!-- Display Error Message -->
                <p style="color: red" class="text-center">
                    <?php if (isset($_GET['error'])) echo $_GET['error']; ?>
                </p>
                <p id="error-message" style="color: red" class="text-center"></p>

                <!-- Email Input -->
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" class="form-control" id="login-email" name="l-email" placeholder="Enter your email" required>
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" class="form-control" id="login-password" name="l-password" placeholder="Enter your password" required>
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <input type="submit" name="login_button" class="btn btn-primary btn-block" id="login-button" value="Login">
                </div>
            </form>

        </div>
    </div>
</section>

<!-- JavaScript Validation -->
<script>
    document.getElementById("login-email").addEventListener("input", validateLoginForm);
    document.getElementById("login-password").addEventListener("input", validateLoginForm);

    function validateLoginForm() {
        var email = document.getElementById("login-email").value;
        var password = document.getElementById("login-password").value;
        var errorMessage = document.getElementById("error-message");

        // Reset error message
        errorMessage.innerHTML = "";

        // Basic email format validation
        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailPattern.test(email)) {
            errorMessage.innerHTML = "Please enter a valid email address.";
            return false;
        }
        if (email === '' || password === '') {
            errorMessage.innerHTML = "Please fill all the empty fields.";
            return false;
        }

        return true;
    }

    document.getElementById("login-form").addEventListener("submit", function(event) {
        if (!validateLoginForm()) {
            event.preventDefault();
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>