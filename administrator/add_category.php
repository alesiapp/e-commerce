<?php
session_start();
include '../includes/connection.php';
include ('header.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['add_category'])) {
    $emri = htmlspecialchars(trim($_POST['name']));
    $detaje = htmlspecialchars(trim($_POST['pershkrimi']));

    if ($emri == '' || $detaje == '') {
        header('Location: add_category.php?error=' . urlencode('Empty Fields'));
        exit();
    }
    $check_query = $conn->prepare('SELECT COUNT(*) FROM kategoria WHERE emri_kategorise = ?');
    $check_query->bind_param('s', $emri);
    $check_query->execute();
    $check_query->bind_result($count);
    $check_query->fetch();
    $check_query->close();

    if ($count > 0) {
        header('Location: add_category.php?error=' . urlencode('Category already exists.'));
        exit();
    }

    $query = $conn->prepare('INSERT INTO kategoria (emri_kategorise, detaje) VALUES (?, ?)');
    $query->bind_param('ss', $emri, $detaje);

    if ($query->execute()) {
        header('Location: categories.php?success=' . urlencode('Category added successfully'));
    } else {
        header('Location: categories.php?error=' . urlencode('Error adding category. Please try again.'));
    }

    $query->close();
    exit();
}
?>


<div class="container-fluid ">
    <?php if (isset($_GET['error'])) { ?>
        <p class="text-center" style="color:red;"><?php echo $_GET['error']; ?></p>
    <?php } ?>
    <div class="row" style="min-height: 1000px">

        <div class="col-lg-6 col-md-12 col-sm-12">
            <form id="add_product" method="post" action="add_category.php" onsubmit="return validateForm()">
                <!-- Name Field -->
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                </div>

                <!-- Description Field -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" id="description" name="pershkrimi" placeholder="Description" required>
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" id="add_button" name="add_category" value="Add Category">
                </div>

                <!-- Error Message Display -->
                <p id="error-message" class="text-danger"></p>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript Validation -->
<script>
    function validateForm() {
        // Get the form fields
        var name = document.getElementById("name").value;
        var description = document.getElementById("description").value;
        var errorMessage = document.getElementById("error-message");

        // Reset error message
        errorMessage.innerHTML = "";

        // Check if the fields are empty
        if (name === "" || description === "") {
            errorMessage.innerHTML = "Please fill out all fields.";
            return false; // Prevent form submission
        }

        // If everything is filled, allow form submission
        return true;
    }

    // Optional: Add real-time validation for each input
    document.getElementById("name").addEventListener("input", function() {
        if (this.value !== "") {
            document.getElementById("error-message").innerHTML = "";
        }
    });

    document.getElementById("description").addEventListener("input", function() {
        if (this.value !== "") {
            document.getElementById("error-message").innerHTML = "";
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>