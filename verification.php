<?php
session_start();

if (!isset($_SESSION['verify'])) {
    header('Location: index.php');
    exit();
}

if (!isset($_SESSION['otp_attempts'])) {
    $_SESSION['otp_attempts'] = 0;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <title>Verification</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
    <div class="container">
        <a class="navbar-brand" href="#">Account Verification</a>
    </div>
</nav>
<main class="login-form">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Verify Your Account</div>
                    <div class="card-body">
<?php echo 'You have '.(3-$_SESSION['otp_attempts']).' attempts left'?>
                        <form action="verification.php" method="POST">

                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <div class="form-group row">
                                <label for="otp" class="col-md-4 col-form-label text-md-right">OTP Code</label>
                                <div class="col-md-6">
                                    <input type="text" id="otp" class="form-control" name="otp_code" required autofocus>
                                </div>
                            </div>

                            <div class="col-md-6 offset-md-4">
                                <input type="submit" value="Verify" name="verify" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>

<?php
include('includes/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }



    $hashed_otp = $_SESSION['otp'];
    $email = $_SESSION['user_email'];
    $otp_code = $_POST['otp_code'];

    if (!password_verify($otp_code, $hashed_otp)) {
        $_SESSION['otp_attempts'] += 1;
        if ($_SESSION['otp_attempts'] >= 3) {
            ?>
            <script>
                alert("<?php echo " Try again later "?>");
            </script>
            <?php
            unset($_SESSION['verify']);
            unset($_SESSION['otp_attempts']);
            unset($_SESSION['otp']);
            header('Location: login.php');
            exit();
        }
        ?>
        <script>
            alert("<?php echo " Invalid Code "?>");
        </script>
        <?php
        header('Location: verification.php');
        exit();
    } else {
        $stmt = $conn->prepare("UPDATE perdorues SET statusi = 1 WHERE email = ?");
        $stmt->bind_param('s', $email);

        if ($stmt->execute()) {
            unset($_SESSION['otp']);
            unset($_SESSION['verify']);
            unset($_SESSION['otp_attempts']);
            header('Location: account.php?success='.urlencode('Account verified'));
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to verify account.";
            unset($_SESSION['otp']);
            unset($_SESSION['verify']);
            unset($_SESSION['otp_attempts']);
            header('Location: verification.php');
            exit();
        }
    }
}
?>
