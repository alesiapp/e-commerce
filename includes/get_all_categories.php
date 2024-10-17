<?php

include "connection.php";
$stmt = $conn->prepare('SELECT kategori_id, emri_kategorise
FROM kategoria k
');
$stmt->execute();
$kategorite = $stmt->get_result();