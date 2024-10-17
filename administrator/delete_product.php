<?php
session_start();
include ('header.php');
include("../includes/connection.php");
if(($_SESSION['admin_logged_in'])!='true'){
header('Location:login.php');
exit();
}
if(isset($_GET['product_id'])){
    $id=$_GET['product_id'];
    $stmt=$conn->prepare('Delete from produkte where id=?');
    $stmt->bind_param('i',$id);
    $stmt->execute();
    if($stmt->execute()){
        header('Location:products.php?success='. urlencode('Product deleted successfully'));
    }
    else{
        header('Location:products.php?error='. urlencode('Error deleting product'));
    }

}