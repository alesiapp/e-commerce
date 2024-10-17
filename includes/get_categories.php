<?php
include "connection.php";
$stmt=$conn->prepare('SELECT k.kategori_id, k.emri_kategorise
FROM kategoria k
JOIN produkte p ON k.kategori_id = p.kategoria
GROUP BY k.kategori_id, k.emri_kategorise
HAVING COUNT(p.id) >= 4;
');
$stmt->execute();
$kategorite=$stmt->get_result();