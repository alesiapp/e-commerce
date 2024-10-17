<?php

session_start();
include('header.php');
include("../includes/connection.php");
if (isset($_GET['order_id'])){
    $id=$_GET['order_id'];
    $stmt1=$conn->prepare('select * from porosi where id= ?');
    $stmt1->bind_param('i',$id);
    $stmt1->execute();
    $orders=$stmt1->get_result();
}
elseif (isset($_POST['edit'])){
    $id=$_POST['id'];
    $statusi=$_POST['statusi'];
    $stmt=$conn->prepare('Update porosi set statusi=? where id=?');
    $stmt->bind_param('si',$statusi,$id);
    if ($stmt->execute()){
        header('location:index.php?success='. urlencode('Order updated successfully'));
    }
    else{
        header('location:index.php?error=' .urlencode('An error occurred while updating order,please try again'));
    }
}

?>
<div class=" container-fluid">
    <div class="row" style="min-height: 1000px">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <form id="edit_product" method="post" action="edit_order.php" >
                <?php foreach($orders as $order) {

                    ?>

                    <input name="id" value="<?php echo $order['id'];?>" type="hidden">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="statusi" class="form-select">

                                <option value="paid" <?php if($order['statusi']=='paid') echo "selected"?>>Paid</option>
                                <option value="shipped" <?php if($order['statusi']=='shipped') echo "selected"?>>Shipped</option>
                                <option value="delivered" <?php if($order['statusi']=='delivered') echo "selected"?>>Delivered</option>

                        </select>

                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary"  id="register-button" name="edit" value="Edit">
                    </div>
                <?php } ?>

            </form></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>