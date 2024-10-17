<?php
include('layouts/header.php');
include('includes/connection.php');

session_start();

if (!isset($_SESSION['logged_in']) && !isset($_POST['change-password'])) {
    header('Location: login.php?error=You are not logged in');
    exit();
}

if (isset($_POST['change-password'])) {
    $new_password = htmlspecialchars($_POST['new_password']);
    $confirmpassword = htmlspecialchars($_POST['confirmpassword']);

    if ($confirmpassword !== $new_password) {
        header('Location: edit_account.php?error=Passwords do not match');
        exit();
    } elseif (strlen($new_password) < 6) {
        header('Location: edit_account.php?error=Password must be at least 6 characters');
        exit();
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt1 = $conn->prepare('UPDATE perdorues SET password = ? WHERE email = ?');
        $stmt1->bind_param('ss', $hashed_password, $_SESSION['user_email']);

        if ($stmt1->execute()) {
            header('Location: account.php?success=Password updated successfully');
            exit();
        } else {
            header('Location: account.php?error=Could not change password');
            exit();
        }
    }
}
?>

<!-- Change Password Form -->
<section class="d-flex justify-content-center align-items-center my-5 py-5">
    <div class="container col-lg-6 col-md-8 col-sm-12 mx-auto">
        <form id="account-form" method="post" action="edit_account.php" class="text-center p-4 shadow rounded bg-light">
            <p class="text-center" style="color: red;" id="error-message"></p>
            <p class="text-center" style="color: green;">
                <?php if (isset($_GET['message'])) echo $_GET['message']; ?>
            </p>
            <h3 class="font-weight-bold mb-4">Change Password</h3>
            <hr class="mx-auto" style="width: 50%;">
            <div class="form-group mt-4">
                <label for="account-password" class="font-weight-bold">New Password</label>
                <input type="password" class="form-control mt-2" id="account-password" placeholder="Enter new password" name="new_password" required>
            </div>
            <div class="form-group mt-4">
                <label for="account-password-confirm" class="font-weight-bold">Confirm Password</label>
                <input type="password" class="form-control mt-2" id="account-password-confirm" placeholder="Confirm new password" name="confirmpassword" required>
            </div>
            <div class="form-group mt-4">
                <input type="submit" value="Change Password" class="btn btn-primary btn-block py-2" id="change-pass-btn" name="change-password">
            </div>
        </form>
    </div>
</section>

<script>
    document.getElementById('account-form').addEventListener('submit', function(event) {
        // Clear previous error messages
        const errorMessage = document.getElementById('error-message');
        errorMessage.textContent = '';

        // Get the input values
        const password = document.getElementById('account-password').value;
        const confirmPassword = document.getElementById('account-password-confirm').value;

        // Check if fields are empty
        if (!password || !confirmPassword) {
            errorMessage.textContent = 'Please fill in all the fields.';
            event.preventDefault(); // Prevent form submission
            return;
        }

        // Check password length
        if (password.length < 6) {
            errorMessage.textContent = 'Password must be at least 6 characters long.';
            event.preventDefault(); // Prevent form submission
            return;
        }

        // Check if passwords match
        if (password !== confirmPassword) {
            errorMessage.textContent = 'Passwords do not match.';
            event.preventDefault(); // Prevent form submission
            return;
        }
    });
</script>

<?php include('layouts/footer.php'); ?>
b