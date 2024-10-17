<?php
include ('../includes/connection.php');
if(!isset($_POST['change-images'])){
    $product_name=$_POST['name'];
    $product_id=$_POST['id'];
    $img1=$_FILES['img1']['tmp_name'];
    $img2=$_FILES['img2']['tmp_name'];
    $img3=$_FILES['img3']['tmp_name'];
    $img4=$_FILES['img4']['tmp_name'];
    $image_name1=$product_name.'1.jpeg';
    $image_name2=$product_name.'2.jpeg';
    $image_name3=$product_name.'3.jpeg';
    $image_name4=$product_name.'4.jpeg';

    move_uploaded_file($img1,'../imgs/'.$image_name1);
    move_uploaded_file($img2,'../imgs/'.$image_name2);
    move_uploaded_file($img3,'../imgs/'.$image_name3);
    move_uploaded_file($img4,'../imgs/'.$image_name4);
    $stmt=$conn->prepare('Update products set foto1=? ,foto2=?,foto3=?,foto4=? where id=?');
    $stmt->bind_param('ssssi',$image_name1,$image_name2,$image_name3,$image_name4,$product_id);
    if ($stmt->execute()){
        header('Location:products.php?success='. urlencode('Images changes successfully'));
        exit();
    }
    else{
        header('Location:products.php?error='. urlencode('Error while changing the images'));
        exit();
    }

}