<?php
include ('layouts/header.php');
?>

<!--home-->
<section id="home">
    <video autoplay muted loop id="bg-video">
        <source src="imgs/Screen%20Recording%202024-09-04%20191915.mp4" type="video/mp4">

    </video>
    <div class="content">
        <h4>NEW ARRIVALS</h4>

        <p>BEST PRODUCTS FOR YOUR SKIN</p>
        <button>Shop Now</button>
    </div>
</section>



<!--home end-->
<!--Brand-->
<section id="gallery">
    <div class="container text-center mt-5 py-5">
        <div class="row justify-content-center">

            <?php include "includes/get_distinct kategories.php";
            while ($r=$categories->fetch_assoc()){
            ?>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                <a href="shop.php?selected=<?php echo $r['kategori_id'];?>" class="gallery-item">
                    <img src="imgs/<?php echo $r['foto1']?>" class="img-fluid fixed-size" alt="Image 1">
                    <p class="image-name"><?php echo $r['emri_kategorise'];?></p>
                </a>
            </div>
<?php }?>
        </div>
    </div>
</section>


<!--New-->
<section id="new" class="w-100">
    <div class="container text-center mt-5 py-5">
        <h3>New In</h3>
        <hr class="mx-auto">
       
    </div>
    <div class="row p-0 m-0">
        <?php include "includes/new.php";?>
        <?php while ($row=$new->fetch_assoc()){?>


        <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
            <img src="imgs/<?php echo $row['foto1'];?>" class="img-fluid">
            <div class="details" >
                <h2><?php echo $row['emri'];?></h2>
               <a href="single_product.php?product_id=<?php echo $row['id'];?>">  <button class="text-uppercase">Shop Now</button></a>
            </div>
        </div>
        <?php }?>

    </div>

</section>


<!--Featured-->
<section id="featured " class="my-5 pb-5">
    <div class="container text-center mt-5 py-5">
        <h3>Our Featured</h3>
        <hr class="mx-auto">
        <p>Here you can check out our new featured products</p>
    </div>
    <div class="row mx-auto container-fluid">
      <?php include('includes/featured_products.php');
      ?>
        <?php while($row=$featured_products->fetch_assoc()){?>

        <div class="product text-center col-lg-3 col-md-3 col-sm-12">
        <img class="img-fluid mb-3" src="imgs/<?php echo $row['foto1'];?>"/>
        <div class="star">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <h5 class="p-name"><?php echo $row['emri']; ?></h5>
        <h4 class="p-price"><?php echo $row['cmimi'] ;?></h4>
        <a href="single_product.php?product_id=<?php echo $row['id'];?>"><button class="buy-btn">Buy Now</button></a>
        </div>

<?php } ?>
    </div>
</section>
=
<?php include ('includes/get_categories.php');
 while ($r=$kategorite->fetch_assoc()){
?>
<!--Serums-->
<section id="<?php echo $r['emri_kategorise'];?>" class="my-5 pb-5">
    <div class="container text-center mt-5 py-5">
        <h3><?php echo strtoupper($r['emri_kategorise']);?></h3>
        <hr class="mx-auto">
        <p>Here you can check out our <?php echo $r['emri_kategorise'];?></p>
    </div>
    <div class="row mx-auto container-fluid">
        <?php include('includes/connection.php');
        $stmt = $conn->prepare('Select * from produkte where kategoria=? limit 4');
        $stmt->bind_param('i',$r['kategori_id']);
        $stmt->execute();
        $products = $stmt->get_result();


        ?>
        <?php while($row=$products->fetch_assoc()){?>
        <div class="product text-center col-lg-3 col-md-3 col-sm-12">
            <img class="img-fluid mb-3" src="imgs/<?php echo $row['foto1'];?>"/>

            <div class="star">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
            </div>
            <h5 class="p-name"><?php echo $row['emri'];?></h5>
            <h4 class="p-price"><?php echo $row['cmimi'];?></h4>
            <a href="single_product.php?product_id=<?php echo $row['id'];?>"><button class="buy-btn">Buy Now</button></a>
        </div>

        <?php };?>

    </div>
</section>
<?php }?>


<?php
include ('layouts/footer.php');
?>


