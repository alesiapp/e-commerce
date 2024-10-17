<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
if(!isset($_SESSION['logged_in'])){
    header('location:login.php');
    exit();
}
else if (!isset($_GET['verify'])){
    header('location:account.php');
    exit();
}

$otp = random_int(100000, 999999);


$hashed_otp = password_hash($otp, PASSWORD_DEFAULT);
$_SESSION['otp'] = $hashed_otp;

require "vendor/phpmailer/phpmailer/src/PHPMailer.php";
$mail = new PHPMailer;
$email=$_SESSION['user_email'];
$mail->isSMTP();
$mail->Host = 'smtp.outlook.com';
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';

$mail->Username = '';
$mail->Password = '';

$mail->setFrom('ecommercealesia@outlook.com', 'Password Reset');
$mail->addAddress($email);

$mail->isHTML(true);
$mail->Subject = "Recover your password";
$mail->Body = "<p>Dear user,</p><h3>Your verification code is $otp</h3><br><p>With regards,</p>";

if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    $_SESSION['verify']=1;
    echo "<script>window.location.replace('verification.php');</script>";
}
