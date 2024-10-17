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
$stmt1=$conn->prepare('select count(*) as total_records from perdorues');
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

$stmt2=$conn->prepare("select * from perdorues  limit $offset,$total_records_per_page");
$stmt2->execute();
$perdorues=$stmt2->get_result();




?>

<div class="container-fluid">
    <?php if(isset($_GET['edit_success_message'])){
        ?>
        <p class="text-center" style="color:green;"><?php echo $_GET['edit_success_message'];?></p>
    <?php } ?>

    <?php if(isset($_GET['edit_failure_message'])){
        ?>
        <p class="text-center" style="color:red;"><?php echo $_GET['edit_failure_message'];?></p>
    <?php } ?>
    <?php if(isset($_GET['delete_successfully'])){
        ?>
        <p class="text-center" style="color:green;"><?php echo $_GET['delete_successfully'];?></p>
    <?php } ?>

    <?php if(isset($_GET['delete_failure'])){
        ?>
        <p class="text-center" style="color:red;"><?php echo $_GET['delete_failure'];?></p>
    <?php } ?>
    <div class="row" style="min-height: 1000px">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="table-responsive">
                <table class="table table-stripped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">Customer Id</th>
                        <th scope="col">Customer Name</th>

                        <th scope="col">Customer Email</th>
                        <th scope="col">Email Verified</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php  foreach ($perdorues as $p) {?>
                        <tr>
                            <td><?php echo $p['id'];?></td>
                            <td><?php echo $p['emri'];?></td>
                            <td><?php echo $p['email']?></td>
                            <td><?php if($p['statusi']==1) echo 'Verified';else echo 'Not Verified';?></td>

                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <!-- Pagination -->
            <nav aria-label="Page navigation example">
                <ul class="pagination mt-5 pt-5">
                    <!-- Previous Button -->
                    <li class="page-item <?php if($page_no <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php if($page_no > 1){ echo '?page_no=' . ($page_no - 1); } else { echo '#'; } ?>">Previous</a>
                    </li>

                    <!-- Page Numbers -->
                    <?php
                    // Show the first page and ellipsis if current page is greater than 3
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

                    // Show ellipsis and the last page if the current page is less than total - 2
                    if ($page_no < $total_number_of_pages - 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        echo '<li class="page-item"><a class="page-link" href="?page_no=' . $total_number_of_pages . '">' . $total_number_of_pages . '</a></li>';
                    }
                    ?>

                    <!-- Next Button -->
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