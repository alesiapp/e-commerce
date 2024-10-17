<?php
include ('layouts/header.php');
include('includes/connection.php');

if (isset($_POST['search'])) {
    // Handle search via POST request
    if (isset($_GET['page_no']) && $_GET['page_no'] != '') {
        $page_no = $_GET['page_no'];
    } else {
        $page_no = 1;
    }
    $stmt1 = $conn->prepare('SELECT COUNT(*) as total_records FROM produkte WHERE kategoria = ? AND cmimi <= ?');
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stmt1->bind_param('si', $category, $price);
    $stmt1->execute();
    $stmt1->bind_result($total_records);
    $stmt1->store_result();
    $stmt1->fetch();

    $total_records_per_page = 2;
    $offset = ($page_no - 1) * $total_records_per_page;
    $total_number_of_pages = ceil($total_records / $total_records_per_page);

    $stmt = $conn->prepare('SELECT * FROM produkte WHERE kategoria = ? AND cmimi <= ? LIMIT ?, ?');
    $stmt->bind_param('siii', $category, $price, $offset, $total_records_per_page);
    $stmt->execute();
    $products = $stmt->get_result();

} elseif (isset($_GET['selected'])) {

    if (isset($_GET['page_no']) && $_GET['page_no'] != '') {
        $page_no = $_GET['page_no'];
    } else {
        $page_no = 1;
    }
    $stmt1 = $conn->prepare('SELECT COUNT(*) as total_records FROM produkte WHERE kategoria = ?');
    $category = $_GET['selected'];
    $stmt1->bind_param('s', $category);
    $stmt1->execute();
    $stmt1->bind_result($total_records);
    $stmt1->store_result();
    $stmt1->fetch();

    $total_records_per_page = 2;
    $offset = ($page_no - 1) * $total_records_per_page;
    $total_number_of_pages = ceil($total_records / $total_records_per_page);

    $stmt = $conn->prepare('SELECT * FROM produkte WHERE kategoria = ? LIMIT ?, ?');
    $stmt->bind_param('sii', $category, $offset, $total_records_per_page);
    $stmt->execute();
    $products = $stmt->get_result();

} else {

    if (isset($_GET['page_no']) && $_GET['page_no'] != '') {
        $page_no = $_GET['page_no'];
    } else {
        $page_no = 1;
    }
    $stmt1 = $conn->prepare('SELECT COUNT(*) as total_records FROM produkte');
    $stmt1->execute();
    $stmt1->bind_result($total_records);
    $stmt1->store_result();
    $stmt1->fetch();

    $total_records_per_page = 2;
    $offset = ($page_no - 1) * $total_records_per_page;
    $total_number_of_pages = ceil($total_records / $total_records_per_page);

    $stmt2 = $conn->prepare('SELECT * FROM produkte LIMIT ?, ?');
    $stmt2->bind_param('ii', $offset, $total_records_per_page);
    $stmt2->execute();
    $products = $stmt2->get_result();
}
?>

<!--Body-->
<div class="container mt-5 py-5">
    <div class="row mx-auto container">
        <?php if (isset($_GET['payment_message'])) { ?>
            <p class="mt-5 text-center" style="color: green;"><?php echo $_GET['payment_message']; ?></p>
        <?php } ?>
        <!-- Search -->
        <div class="col-lg-3 col-md-4 col-sm-12">
            <section id="search" class="my-2 py-2">
                <div class="container mt-5 py-5">
                    <h3>Search Product</h3>
                    <hr>
                    <p>Here you can search our products</p>
                </div>
                <div class="row">
                    <form action="shop.php" method="post">
                        <div class="row mx-auto container">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <p>Category</p>
                                <?php include('includes/get_categories.php');
                                while ($row = $kategorite->fetch_assoc()) { ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="category" value="<?php echo $row['kategori_id']; ?>" id="<?php echo $row['kategori_id']; ?>">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            <?php echo $row['emri_kategorise']; ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="row mx-auto container mt-5">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <p>Price: <span id="priceValue">20</span></p>
                                <input type="range" name="price" value="20" class="form-range w-50" min="1" max="50" id="customRange2" oninput="updatePriceValue(this.value)">
                                <div class="w-50">
                                    <span style="float: left">1</span>
                                    <span style="float: right">50</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group my-3 mx-3">
                            <input type="submit" name="search" value="Search" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <!-- Shop -->
        <div class="col-lg-9 col-md-8 col-sm-12">
            <section id="shop" class="my-2 py-2">
                <div class="container mt-5 py-5">
                    <h3>Our Products</h3>
                    <hr>
                    <p>Here you can check out our products</p>
                </div>
                <div class="row mx-auto container">
                    <?php while ($row = $products->fetch_assoc()) { ?>
                        <!-- featured 1 -->
                        <div class="product text-center col-lg-3 col-md-6 col-sm-12 mb-5 pb-5" onclick="window.location.href='single_product.php?product_id=<?php echo $row['id']; ?>'">
                            <img class="img-fluid mb-3" src="imgs/<?php echo $row['foto1']; ?>" />
                            <div class="star">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <h5 class="p-name"><?php echo $row['emri']; ?></h5>
                            <h4 class="p-price"><?php echo $row['cmimi']; ?></h4>
                            <a class="btn buy-btn" href="single_product.php?product_id=<?php echo $row['id']; ?>">Buy Now</a>
                        </div>
                    <?php } ?>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination mt-5 pt-5">
                            <li class="page-item <?php if ($page_no <= 1) echo 'disabled'; ?>">
                                <a class="page-link" href="<?php if ($page_no > 1) {
                                    echo '?page_no=' . ($page_no - 1);
                                    if (isset($category)) echo '&selected=' . $category;
                                    if (isset($price)) echo '&price=' . $price;
                                } else { echo '#'; } ?>">Previous</a>
                            </li>

                            <?php for ($i = 1; $i <= $total_number_of_pages; $i++) { ?>
                                <li class="page-item <?php if ($page_no == $i) echo 'active'; ?>">
                                    <a class="page-link" href="?page_no=<?php echo $i;
                                    if (isset($category)) echo '&selected=' . $category;
                                    if (isset($price)) echo '&price=' . $price; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php } ?>

                            <li class="page-item <?php if ($page_no >= $total_number_of_pages) echo 'disabled'; ?>">
                                <a class="page-link" href="<?php if ($page_no < $total_number_of_pages) {
                                    echo '?page_no=' . ($page_no + 1);
                                    if (isset($category)) echo '&selected=' . $category;
                                    if (isset($price)) echo '&price=' . $price;
                                } else { echo '#'; } ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </section>
        </div>

    </div>
</div>
<script>
    function updatePriceValue(val) {
        document.getElementById('priceValue').innerText = val;
    }
</script>
<?php include ('layouts/footer.php'); ?>
