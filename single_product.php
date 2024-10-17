
<?php
include ('layouts/header.php');
include('includes/connection.php');

if(isset($_GET['product_id'])){
    $product_id=$_GET['product_id'];


    $stmt = $conn->prepare('SELECT * FROM produkte WHERE id = ?');
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $product = $stmt->get_result();
} else {
    header('location:index.php');
}
?>

<!--Single Product-->
<section class="container single_product my-5 pt-5">
    <div class="row mt-5">
        <?php while ($row = $product->fetch_assoc()) {
            $category = $row['kategoria'];
            $available_quantity = $row['gjendja']; // Available quantity from database
            ?>

            <div class="col-lg-6 col-md-6 col-sm-12">
                <img id="mainImg" src="imgs/<?php echo $row['foto1'];?>" class="img-fluid w-100 pb-1">
                <div class="small-img-group">
                    <!-- Small images -->
                    <div class="small-img-col">
                        <img src="imgs/<?php echo $row['foto1'];?>" width="100%" class="small-img">
                    </div>
                    <div class="small-img-col">
                        <img src="imgs/<?php echo $row['foto2'];?>" width="100%" class="small-img">
                    </div>
                    <div class="small-img-col">
                        <img src="imgs/<?php echo $row['foto3'];?> " width="100%" class="small-img">
                    </div>
                    <div class="small-img-col">
                        <img src="imgs/<?php echo $row['foto4'];?> " width="100%" class="small-img">
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12">
                <h6><?php echo $row['kategoria'];?></h6>
                <h3 class="py-4"><?php echo $row['emri'];?></h3>
                <h2><?php echo $row['cmimi'];?>$</h2>

                <form method="post" action="cart.php">
                    <input type="hidden" name="product_image" value="<?php echo $row['foto1'];?>">
                    <input type="hidden" name="product_id" value="<?php echo $row['id'];?>">
                    <input type="hidden" name="product_name" value="<?php echo $row['emri'];?>">
                    <input type="hidden" name="product_price" value="<?php echo $row['cmimi'];?>">


                    <input type="number" value="1" name="product_quantity" min="1" max="<?php echo $available_quantity;?>">

                    <button class="buy-btn" type="submit" name="add_to_cart">Add To Cart</button>
                </form>

                <h4 class="mt-5 mb-5">Product Details</h4>
                <span><?php echo $row['pershkrimi'];?></span>
            </div>

        <?php } ?>
    </div>
</section>

<!-- Related Products -->
<section id="related-products" class="my-5 pb-5">
    <div class="container text-center mt-5 py-5">
        <h3>Related Products</h3>
        <hr class="mx-auto">
    </div>
    <div class="row mx-auto container-fluid">
        <?php
        $stmt = $conn->prepare('SELECT * FROM produkte WHERE kategoria = ? and id!=? LIMIT 4');
        $stmt->bind_param('si', $category,$product_id);
        $stmt->execute();
        $pr = $stmt->get_result();

        while ($row1 = $pr->fetch_assoc()) {
            ?>
            <div class="product text-center col-lg-3 col-md-3 col-sm-12">
                <img class="img-fluid mb-3" src="imgs/<?php echo $row1['foto1'];?>">
                <div class="star">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <h5 class="p-name"><?php echo $row1['emri'];?></h5>
                <h4 class="p-price"><?php echo $row1['cmimi'];?>$</h4>
                <a href="single_product.php?product_id=<?php echo $row1['id'];?>">
                    <button class="buy-btn">Buy Now</button>
                </a>
            </div>
        <?php } ?>
    </div>
</section>

<!-- Reviews Section -->
<section id="reviews" class="my-5 pb-5">
    <div class="container text-center mt-5 py-5">
        <h3 class="font-weight-bold">Product Reviews</h3>
        <hr class="mx-auto" style="width: 60px; border-top: 3px solid #333;">
    </div>
    <div class="row justify-content-center mx-auto container-fluid">
        <?php
        // Pagination setup
        $limit = 3; // Number of reviews per page
        $page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
        $offset = ($page - 1) * $limit; // Offset for the SQL query

        // Get total number of reviews for this product
        $stmt_total = $conn->prepare('SELECT COUNT(*) FROM reviews WHERE id_produkti = ?');
        $stmt_total->bind_param('i', $product_id);
        $stmt_total->execute();
        $stmt_total->bind_result($total_reviews);
        $stmt_total->fetch();
        $stmt_total->close();

        // Fetch reviews with limit and offset for pagination
        $stmt_reviews = $conn->prepare('SELECT r.*, u.emri FROM reviews r JOIN perdorues u ON r.id_perdoruesi = u.id WHERE id_produkti = ? LIMIT ? OFFSET ?');
        $stmt_reviews->bind_param('iii', $product_id, $limit, $offset);
        $stmt_reviews->execute();
        $result_reviews = $stmt_reviews->get_result();

        if ($result_reviews->num_rows > 0) {
            while ($review = $result_reviews->fetch_assoc()) {
                ?>
                <div class="col-md-10 col-lg-8">
                    <div class="card my-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="font-weight-bold text-center"><?php echo htmlspecialchars($review['emri'] ?? 'Anonymous'); ?></h5>
                            <h5 class="font-weight-bold text-primary text-center">Rating: <?php echo htmlspecialchars($review['vleresimi'] ?? 'N/A'); ?>/5</h5>
                            <p class="text-muted text-center"><?php echo htmlspecialchars($review['pershkrimi'] ?? 'No review provided.'); ?></p>

                            <br>
                            <p class=" text-center">Posted on: <?php echo htmlspecialchars($review['data'] ?? 'Unknown date'); ?></p>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='text-center'>No reviews yet for this product.</p>";
        }
        ?>
    </div>

    <!-- Pagination -->
    <div class="container text-center mt-4">
        <ul class="pagination justify-content-center">
            <?php
            // Calculate total pages
            $total_pages = ceil($total_reviews / $limit);

            // Display previous button
            if ($page > 1) {
                echo '<li class="page-item"><a class="page-link" href="single_product.php?product_id=' . $product_id . '&page=' . ($page - 1) . '">Previous</a></li>';
            }

            // Display page numbers
            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="single_product.php?product_id=' . $product_id . '&page=' . $i . '">' . $i . '</a></li>';
            }

            // Display next button
            if ($page < $total_pages) {
                echo '<li class="page-item"><a class="page-link" href="single_product.php?product_id=' . $product_id . '&page=' . ($page + 1) . '">Next</a></li>';
            }
            ?>
        </ul>
    </div>
</section>




<script>
    var mainImg = document.getElementById('mainImg');
    var smallImg = document.getElementsByClassName('small-img');
    for (let i = 0; i < 4; i++) {
        smallImg[i].onclick = function() {
            mainImg.src = smallImg[i].src;
        }
    }
</script>

<?php
include ('layouts/footer.php');
?>

