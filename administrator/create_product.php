<?php
global $conn;
include('../includes/connection.php');

if (isset($_POST['add'])) {
    $errors = [];


    $product_name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $product_price = htmlspecialchars(trim($_POST['cmimi']), ENT_QUOTES, 'UTF-8');
    $product_description = htmlspecialchars(trim($_POST['pershkrimi']), ENT_QUOTES, 'UTF-8');
    $product_category = htmlspecialchars(trim($_POST['kategoria']), ENT_QUOTES, 'UTF-8');

    if (empty($product_name)) {
        $errors[] = "Name is required.";
    }

    if (empty($product_price) || !is_numeric($product_price)) {
        $errors[] = "A valid price is required.";
    }

    if (empty($product_description)) {
        $errors[] = "Description is required.";
    }

    // Validate images
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    for ($i = 1; $i <= 4; $i++) {
        $img = "img" . $i;
        if (empty($_FILES[$img]['name'])) {
            $errors[] = "Image $i is required.";
        } else {
            $ext = pathinfo($_FILES[$img]['name'], PATHINFO_EXTENSION);
            if (!in_array($ext, $allowedExtensions)) {
                $errors[] = "Invalid file type for Image $i. Only JPG, JPEG, PNG, and GIF are allowed.";
            }
        }
    }

    if (empty($product_category)) {
        $errors[] = "Category is required.";
    }


    if (!empty($errors)) {
        $errorString = urlencode(implode(", ", $errors));
        header("Location: add_product.php?error=$errorString");
        exit();
    }


    $stmt_check = $conn->prepare('SELECT COUNT(*) FROM produkte WHERE emri = ?');
    $stmt_check->bind_param('s', $product_name);
    $stmt_check->execute();
    $stmt_check->bind_result($product_exists);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($product_exists > 0) {
        header('Location: add_product.php?error=' . urlencode('Product with the same name already exists.'));
        exit();
    }

    $img1 = $_FILES['img1']['tmp_name'];
    $img2 = $_FILES['img2']['tmp_name'];
    $img3 = $_FILES['img3']['tmp_name'];
    $img4 = $_FILES['img4']['tmp_name'];

    $image_name1 = htmlspecialchars($product_name . '_1.' . pathinfo($_FILES['img1']['name'], PATHINFO_EXTENSION), ENT_QUOTES, 'UTF-8');
    $image_name2 = htmlspecialchars($product_name . '_2.' . pathinfo($_FILES['img2']['name'], PATHINFO_EXTENSION), ENT_QUOTES, 'UTF-8');
    $image_name3 = htmlspecialchars($product_name . '_3.' . pathinfo($_FILES['img3']['name'], PATHINFO_EXTENSION), ENT_QUOTES, 'UTF-8');
    $image_name4 = htmlspecialchars($product_name . '_4.' . pathinfo($_FILES['img4']['name'], PATHINFO_EXTENSION), ENT_QUOTES, 'UTF-8');

    move_uploaded_file($img1, '../imgs/' . $image_name1);
    move_uploaded_file($img2, '../imgs/' . $image_name2);
    move_uploaded_file($img3, '../imgs/' . $image_name3);
    move_uploaded_file($img4, '../imgs/' . $image_name4);

    // Insert the product
    $stmt = $conn->prepare('INSERT INTO produkte (emri, pershkrimi, cmimi, foto1, foto2, foto3, foto4, kategoria) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('ssissssi', $product_name, $product_description, $product_price,
        $image_name1, $image_name2, $image_name3, $image_name4, $product_category);

    if ($stmt->execute()) {
        header('Location: products.php?success=' . urlencode('Product created successfully'));
        exit();
    } else {
        header('Location: add_product.php?error=' . urlencode('Error creating product, please try again.'));
        exit();
    }
}
?>
