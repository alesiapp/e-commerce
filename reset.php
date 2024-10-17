<?php include "layouts/header.php"; ?>





    <section>
        <div class="container text-center pt-5 mt-5 pb-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Password Recovery</div>
                        <div class="card-body">
                            <form action="reset.php" method="POST" name="recover_psw">
                                <div class="form-group">
                                    <label for="email_address" class="col-form-label">E-Mail Address</label>
                                    <input type="email" id="email_address" class="form-control" name="email" required autofocus>
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary w-100" value="Recover" name="recover">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


<?php include 'layouts/footer.php';?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
if(isset($_POST["recover"])){
    include('includes/connection.php');
    $email = $_POST["email"];

    $sql = mysqli_query($conn, "SELECT * FROM perdorues WHERE email='$email'");
    $query = mysqli_num_rows($sql);
    $fetch = mysqli_fetch_assoc($sql);

    if(mysqli_num_rows($sql) <= 0){
        ?>
        <script>
            alert("<?php  echo "Sorry, there is no account with that email "?>");
        </script>
        <?php
    }else if($fetch["statusi"] != 1){
        ?>
        <script>
            alert("Sorry, your account must verify first, before you recover your password !");
            window.location.replace("index.php");
        </script>
        <?php
    }else{

        $token = bin2hex(random_bytes(50));

        $_SESSION['token'] = $token;
        $_SESSION['email'] = $email;

        require "vendor/phpmailer/phpmailer/src/PHPMailer.php";

        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host='smtp.outlook.com';
        $mail->Port=587;
        $mail->SMTPAuth=true;
        $mail->SMTPSecure='tls';
        $mail->Username='';
        $mail->Password='';
        $mail->setFrom('ecommercealesia@outlook.com', 'Password Reset');
        $mail->addAddress($_POST["email"]);

        $mail->isHTML(true);
        $mail->Subject="Recover your password";
        $mail->Body= $mail->Body = "<p>Dear user, </p> <h3>Your verification code is $token <br></h3><br><br><p>With regards,</p><b></b>";

        if(!$mail->send()){
            ?>
            <script>
                alert("<?php echo " Invalid Email "?>");
            </script>
            <?php
        }else{
            ?>
            <script>
                window.location.replace("get_code.php?getcode=1");
            </script>
            <?php
        }
    }
}


?>