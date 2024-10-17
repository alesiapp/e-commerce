<?php
include ('layouts/header.php');
include('includes/connection.php');
if (isset($_POST['order_details']) && isset($_POST['order_id'])){
    $order_id=$_POST['order_id'];
    $order_status=$_POST['order_status'];
  $stmt=  $conn->prepare( 'Select id_porosi,p.emri,p.foto1,p.id,p.cmimi,o.sasi_produkti 
from produkte_porosi o join produkte p on o.id_produkti=p.id  where o.id_porosi=?');
  $stmt->bind_param('i',$order_id);
  $stmt->execute();
  $order_details=$stmt->get_result();
  $order_total_price=calculateTotalOrderPrice($order_details);
}
else{
    header('location:account.php');
    exit;
}
function calculateTotalOrderPrice($order_details)
{
    $total=0;
    foreach ($order_details as $row){
        $product_price=$row['cmimi'];
        $product_quantity=$row['sasi_produkti'];
        $total=$total+($product_quantity*$product_price);
    }
    $order_details->data_seek(0);
    return $total;
}
?>
<!--Order details-->
<section id="order-details" class="orders container my-5 py-3">
    <div class="container mt-5">
        <h2 class="font-weight-bold text-center">Order details</h2>
        <hr class="mx-auto">
    </div>
    <table class="mt-5 pt-5 mx-auto">
        <tr>
            <th>Product Name</th>
            <th>Product Price</th>
            <th>Product Quantity</th>
        </tr>
            <tr>
                <?php while($row=$order_details->fetch_assoc()){?>
                <td>
                    <div class="product-info">
                        <img src="imgs/<?php echo $row['foto1']?>">
                    <div>
                        <p class="mt-3"><?php  echo $row['emri'];?></p>
                    </div>
                    </div>
                </td>
                <td>
                    <span><?php  echo $row['cmimi'];?></span>
                </td>
                <td>
                    <span><?php  echo $row['sasi_produkti'];?></span>
                </td>
            </tr>
 <?php } ?>
    </table>
    <?php if ($order_status == 'delivered') { ?>
        <form style="float: right" method="post" action="review.php">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <input type="submit" name="order_review_btn" value="Review Products" class="btn btn-success">
        </form>
    <?php } ?>
</section>
<?php
include ('layouts/footer.php');
?>