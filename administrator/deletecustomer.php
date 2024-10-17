<?php
session_start();
include '../includes/connection.php';
if(!isset($_SESSION['admin_logged_in'])){
    header('Location:login.php?error=You should log in first');
}
if(!isset($_GET['customer_id'])){
    header('Location:unverified.php');
}
$id_perdoruesit=$_GET['customer_id'];
$stmt1=$conn->prepare('Delete from perdorues where id=?');
$stmt1->bind_param('i',$id_perdoruesit);
if($stmt1->execute()){
    header('Location:unverified.php?success='.urlencode('Customer deleted successfully'));
}
else{
    header('Location:unverified.php?error='. urlencode('Error deleting customer'));
}