<?php
include ('connection.php');

$query = "SELECT * FROM produkte LIMIT 4";
$featured_products = mysqli_query($conn, $query);

