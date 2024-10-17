<?php
include ('connection.php');

$query = "SELECT * FROM produkte order by krijuar asc LIMIT 3 ";
$new = mysqli_query($conn, $query);