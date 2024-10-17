<?php

session_start();
include('header.php');
include("../includes/connection.php");
if (isset($_GET['product_id'])){
    $id=$_GET['product_id'];
$stmt1=$conn->prepare('select * from produkte where id= ?');
$stmt1->bind_param('i',$id);
$stmt1->execute();
$products=$stmt1->get_result();
}
elseif (isset($_POST['edit'])){
    $id=$_POST['product_id'];
    $emri=$_POST['name'];
    $pershkrimi=$_POST['pershkrimi'];
    $cmimi=$_POST['cmimi'];
    $kategoria=$_POST['kategoria'];
    $stmt=$conn->prepare('Update produkte set emri=?,pershkrimi=?,cmimi=?,kategoria=?
where id=?');
    $stmt->bind_param('ssiii',$emri,$pershkrimi,$cmimi,$kategoria,$id);
    if ($stmt->execute()){
        header('location:products.php?success='. urlencode('Product edited successfully'));
    }
    else{
        header('location:products.php?error=Error occurred while updating product,please try again');
    }
}

?>
<div class=" container-fluid">
    <div class="row" style="min-height: 1000px">
        <div class="col-lg-12 col-md-12 col-sm-12">
    <form id="edit_product" method="post" action="edit_product.php" >
        <?php foreach($products as $product) {

        ?>


<div class="form-group">
    <label>Name</label>
    <input type="text" class="form-control" value="<?php echo $product['emri'];?>" id="name" name="name" placeholder="Name" required>
</div>
            <input name="product_id" value="<?php echo $product['id'];?>" type="hidden">
<div class="form-group">
    <label>Price</label>
    <input type="number" class="form-control" value="<?php echo $product['cmimi'];?>" id="cmimi" name="cmimi" placeholder="Price" required>
</div>
        <div class="form-group">
            <label>Description</label>
            <input type="text" class="form-control" value="<?php echo $product['pershkrimi'];?>" id="description" name="pershkrimi" placeholder="Description" required>
        </div>
<div class="form-group">
    <label>Kategoria</label>
    <select name="kategoria" class="form-select">
    <?php include ('../includes/get_categories.php');
    while($row=$kategorite->fetch_assoc()){
    ?>
       <option value="<?php echo $row['kategori_id'];?>"><?php echo $row['emri_kategorise'];?></option>
    <?php  }?>
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