<?php
session_start();
include('header.php');
include("../includes/connection.php");

if ($_SESSION['admin_logged_in'] != 'true') {
    header('Location:login.php');
    exit();
}

// Pagination Logic
if (isset($_GET['page_no']) && $_GET['page_no'] != '') {
    $page_no = $_GET['page_no'];
} else {
    $page_no = 1;
}

$n = 0;


$stmt1 = $conn->prepare('SELECT COUNT(*) AS total_records FROM perdorues WHERE statusi = ?');
$stmt1->bind_param('i', $n);
$stmt1->execute();
$stmt1->bind_result($total_records);
$stmt1->store_result();
$stmt1->fetch();
$stmt1->close();

$total_records_per_page = 5;
$offset = ($page_no - 1) * $total_records_per_page;
$total_number_of_pages = ceil($total_records / $total_records_per_page);


$stmt2 = $conn->prepare("SELECT * FROM perdorues WHERE statusi = ? LIMIT ?, ?");
$stmt2->bind_param('iii', $n, $offset, $total_records_per_page);
$stmt2->execute();
$perdorues = $stmt2->get_result();
$stmt2->close();

?>

<div class="container-fluid text-center">
    <!-- Display any success or error messages -->
    <?php if (isset($_GET['success'])) { ?>
        <p class="text-center" style="color:green;"><?php echo $_GET['success']; ?></p>
    <?php } ?>
    <?php if (isset($_GET['error'])) { ?>
        <p class="text-center" style="color:red;"><?php echo $_GET['error']; ?></p>
    <?php } ?>

    <div class="row" style="min-height: 1000px" >
        <div class="col-lg-10 col-md-12 col-sm-12">
            <div class="table-responsive">
                <table class="table table-stripped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">Customer Id</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Customer Email</th>
                        <th scope="col">Pending Orders</th>
                        <th scope="col">Delete User</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($perdorues as $p) { ?>
                        <tr>
                            <td><?php echo $p['id']; ?></td>
                            <td><?php echo $p['emri']; ?></td>
                            <td><?php echo $p['email']; ?></td>
                            <?php
                            // Fetch order count
                            $order_count = 0;
                            $query = $conn->prepare('SELECT COUNT(id) FROM porosi WHERE id_perdoruesi = ?');
                            $query->bind_param('i', $p['id']);
                            if ($query->execute()) {
                                $query->bind_result($order_count);
                                $query->fetch();
                            }
                            $query->close();
                            ?>
                            <td><?php echo $order_count; ?></td>
                            <td><a href="deletecustomer.php?customer_id=<?php echo $p['id']; ?>" class="btn btn-danger">Delete</a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation example">
                <ul class="pagination mt-5 pt-5">
                    <!-- Previous Button -->
                    <li class="page-item <?php if ($page_no <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php if ($page_no > 1) { echo '?page_no=' . ($page_no - 1); } else { echo '#'; } ?>">Previous</a>
                    </li>

                    <!-- Page Numbers -->
                    <?php
                    if ($page_no > 3) {
                        echo '<li class="page-item"><a class="page-link" href="?page_no=1">1</a></li>';
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }

                    for ($i = max(1, $page_no - 2); $i <= min($page_no + 2, $total_number_of_pages); $i++) {
                        echo '<li class="page-item ' . ($i == $page_no ? 'active' : '') . '"><a class="page-link" href="?page_no=' . $i . '">' . $i . '</a></li>';
                    }

                    if ($page_no < $total_number_of_pages - 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        echo '<li class="page-item"><a class="page-link" href="?page_no=' . $total_number_of_pages . '">' . $total_number_of_pages . '</a></li>';
                    }
                    ?>

                    <!-- Next Button -->
                    <li class="page-item <?php if ($page_no >= $total_number_of_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php if ($page_no < $total_number_of_pages) { echo '?page_no=' . ($page_no + 1); } else { echo '#'; } ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>