<?php session_start();
include('includes/connection.php');
if (!isset($_SESSION['otp_attempts'])){
    $_SESSION['otp_attempts']=0;
}
?>

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="Favicon.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <title>Verification</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
    <div class="container">
        <a class="navbar-brand" href="#">Verification Account</a>
    </div>
</nav>

<main class="login-form">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Verification Account</div>
                    <div class="card-body">
                        <?php

                        if (isset($_POST["verify1"])) {
                            $otp_code = $_POST['otp_code'];
                            $session_otp = isset($_SESSION['token']) ? $_SESSION['token'] : null;

                            if ($session_otp !== $otp_code) {
                                $_SESSION['otp_attempts']=$_SESSION['otp_attempts']+1;
                                echo "<script>alert('Try again');</script>";
                                header('Location:get_code.php');
                            } else {
                                unset($_SESSION['otp_attempts']);

                                echo '
                                <form action="reset_password.php" method="POST">
                                    <div class="form-group row">
                                        <label for="new_password" class="col-md-4 col-form-label text-md-right">New Password</label>
                                        <div class="col-md-6">
                                            <input type="password" id="new_password" class="form-control" name="n_password" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="confirm_password" class="col-md-4 col-form-label text-md-right">Confirm Password</label>
                                        <div class="col-md-6">
                                            <input type="password" id="confirm_password" class="form-control" name="conf_password" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 offset-md-4">
                                        <input type="submit" value="Change Password" name="change_password" class="btn btn-primary">
                                    </div>
                                </form>';
                            }
                        }


                        if (isset($_POST["change_password"])) {
                            $new_password = htmlspecialchars($_POST['n_password']);
                            $confirm_password = htmlspecialchars($_POST['conf_password']);
                            $email = $_SESSION['email'];

                            if ($new_password !== $confirm_password) {
                                echo "<script>alert('Passwords do not match');</script>";
                            } elseif (strlen($new_password) < 6) {
                                echo "<script>alert('Password must be at least 6 characters long');</script>";
                            } else {
                               -
                                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);


                                $stmt = $conn->prepare("UPDATE perdorues SET password = ? WHERE email = ?");
                                $stmt->bind_param('ss', $hashed_password, $email);

                                if ($stmt->execute()) {
                                    echo "<script>alert('Password changed successfully');</script>";
                                    unset($_SESSION['token']);
                                    header('Location: login.php');
                                    exit();
                                } else {
                                    echo "<script>alert('Failed to change password');</script>";
                                    unset($_SESSION['token']);
                                    header('Location: login.php');
                                    exit();
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>