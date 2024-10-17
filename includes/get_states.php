<?php

include "connection.php";
$stmt = $conn->prepare('Select * from shteti
');
$stmt->execute();
$states = $stmt->get_result();