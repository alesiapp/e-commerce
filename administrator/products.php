<?php
session_start();
include ('header.php');
include("../includes/connection.php");
if(($_SESSION['admin_logged_in'])!='true'){
    header('Location:login.php');
    exit();
}
if(isset($_GET['page_no'])&& $_GET['page_no']!=''){
    $page_no=$_GET['page_no'];
}else{
    $page_no=1;
}
$stmt1=$conn->prepare('select count(*) as total_records from produkte');
$stmt1->execute();
$stmt1->bind_result($total_records);
$stmt1->store_result();
$stmt1->fetch();
$total_records_per_page=5;
$offset=($page_no-1)*$total_records_per_page;
$previous_page=$page_no-1;
$next_page=$page_no+1;
$adjacents='2';
$total_number_of_pages=ceil($total_records/$total_records_per_page);

$stmt2=$conn->prepare("select * from produkte  limit $offset,$total_records_per_page");
$stmt2->execute();
$produkte=$stmt2->get_result();




?>
<style>
    img{
        width: 50px;
        height: 50px;
    }
</style>
<div class="container-fluid">
    <?php if(isset($_GET['success'])){
        ?>
        <p class="text-center" style="color:green;"><?php echo $_GET['success'];?></p>
    <?php } ?>

    <?php if(isset($_GET['error'])){
        ?>
        <p class="text-center" style="color:red;"><?php echo $_GET['error'];?></p>
    <?php } ?>

    <div class="row" style="min-height: 1000px">

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="table-responsive">
                <table class="table table-stripped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">Product Id</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Product Image</th>
                        <th scope="col">Product Category</th>
                        <th scope="col">Product Price</th>
                        <th scope="col">Edit images</th>


                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php  foreach ($produkte as $product) {?>
                        <tr>
                            <td><?php echo $product['id'];?></td>
                            <td><?php echo $product['emri'];?></td>
                            <td><img src="../imgs/<?php echo $product['foto1'];?>"></td>
                            <td><?php echo $product['kategoria']?></td>
                            <td><?php echo $product['cmimi'];?></td>
                            <td><a class="btn btn-primary " style="background-color: #495057" href="edit_images.php?product_id=<?php echo $product['id'];?>.&?product_name=<?php echo $product['emri'];?>">Edit Images</a></td>
                            <td><a class="btn btn-primary " href="edit_product.php?product_id=<?php echo $product['id'];?>">Edit</a></td>
                            <td><a class="btn btn-danger"  href="delete_product.php?product_id=<?php echo $product['id'];?>">Delete</a></td>

                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->

            <nav aria-label="Page navigation example">
                <ul class="pagination mt-5 pt-5">

                    <li class="page-item <?php if($page_no <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php if($page_no > 1){ echo '?page_no=' . ($page_no - 1); } else { echo '#'; } ?>">Previous</a>
                    </li>


                    <?php

                    if ($page_no > 3) {
                        echo '<li class="page-item"><a class="page-link" href="?page_no=1">1</a></li>';
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }

                    // Show current page number and 2 pages before and after it
                    for ($i = max(1, $page_no - 2); $i <= min($page_no + 2, $total_number_of_pages); $i++) {
                        echo '<li class="page-item ' . ($i == $page_no ? 'active' : '') . '">
                    <a class="page-link" href="?page_no=' . $i . '">' . $i . '</a>
                  </li>';
                    }


                    if ($page_no < $total_number_of_pages - 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        echo '<li class="page-item"><a class="page-link" href="?page_no=' . $total_number_of_pages . '">' . $total_number_of_pages . '</a></li>';
                    }
                    ?>


                    <li class="page-item <?php if($page_no >= $total_number_of_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php if($page_no < $total_number_of_pages){ echo '?page_no=' . ($page_no + 1); } else { echo '#'; } ?>">Next</a>
                    </li>
                </ul>
            </nav>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>