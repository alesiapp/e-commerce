<?php
session_start();
include ('header.php');
if (!isset($_SESSION['admin_logged_in'])){
    header('Location:login.php');
    exit();
}
?>

<div class="container-fluid ">
    <div class="row" style="min-height: 1000px">

        <div class="col-lg-6 col-md-12 col-sm-12 ">
            <form id="add_product" method="post" enctype="multipart/form-data" action="create_product.php" onsubmit="return validateProductForm()">
                <!-- Name Field -->
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                </div>

                <!-- Price Field -->
                <div class="form-group">
                    <label for="cmimi">Price</label>
                    <input type="number" class="form-control" id="cmimi" name="cmimi" placeholder="Price" required>
                </div>

                <!-- Description Field -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" id="description" name="pershkrimi" placeholder="Description" required>
                </div>

                <!-- Image Fields -->
                <div class="form-group">
                    <label for="img1">Image 1</label>
                    <input type="file" class="form-control" id="img1" name="img1" required>
                </div>

                <div class="form-group">
                    <label for="img2">Image 2</label>
                    <input type="file" class="form-control" id="img2" name="img2" required>
                </div>

                <div class="form-group">
                    <label for="img3">Image 3</label>
                    <input type="file" class="form-control" id="img3" name="img3" required>
                </div>

                <div class="form-group">
                    <label for="img4">Image 4</label>
                    <input type="file" class="form-control" id="img4" name="img4" required>
                </div>

                <!-- Category Dropdown -->
                <div class="form-group">
                    <label for="kategoria">Category</label>
                    <select name="kategoria" class="form-select" id="kategoria" required>
                        <option value="">Select Category</option>
                        <?php include('../includes/get_all_categories.php');
                        while ($row = $kategorite->fetch_assoc()) { ?>
                            <option value="<?php echo $row['kategori_id']; ?>"><?php echo $row['emri_kategorise']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" id="register-button" name="add" value="Add Product">
                </div>

                <!-- Error Message Display -->
                <p id="error-message" class="text-danger"></p>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript Validation -->
<script>
    function validateProductForm() {
        // Get the form fields
        var name = document.getElementById("name").value;
        var price = document.getElementById("cmimi").value;
        var description = document.getElementById("description").value;
        var img1 = document.getElementById("img1").value;
        var img2 = document.getElementById("img2").value;
        var img3 = document.getElementById("img3").value;
        var img4 = document.getElementById("img4").value;
        var category = document.getElementById("kategoria").value;
        var errorMessage = document.getElementById("error-message");

        // Reset error message
        errorMessage.innerHTML = "";

        // Check if any field is empty
        if (name === "" || price === "" || description === "" || img1 === "" || img2 === "" || img3 === "" || img4 === "" || category === "") {
            errorMessage.innerHTML = "Please fill out all fields.";
            return false; // Prevent form submission
        }

        // If everything is filled, allow form submission
        return true;
    }

    // Optional: Add real-time validation for each input
    document.getElementById("name").addEventListener("input", function () {
        if (this.value !== "") {
            document.getElementById("error-message").innerHTML = "";
        }
    });

    document.getElementById("cmimi").addEventListener("input", function () {
        if (this.value !== "") {
            document.getElementById("error-message").innerHTML = "";
        }
    });

    document.getElementById("description").addEventListener("input", function () {
        if (this.value !== "") {
            document.getElementById("error-message").innerHTML = "";
        }
    });

    document.getElementById("img1").addEventListener("change", function () {
        if (this.value !== "") {
            document.getElementById("error-message").innerHTML = "";
        }
    });

    document.getElementById("img2").addEventListener("change", function () {
        if (this.value !== "") {
            document.getElementById("error-message").innerHTML = "";
        }
    });

    document.getElementById("img3").addEventListener("change", function () {
        if (this.value !== "") {
            document.getElementById("error-message").innerHTML = "";
        }
    });

    document.getElementById("img4").addEventListener("change", function () {
        if (this.value !== "") {
            document.getElementById("error-message").innerHTML = "";
        }
    });

    document.getElementById("kategoria").addEventListener("change", function () {
        if (this.value !== "") {
            document.getElementById("error-message").innerHTML = "";
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>