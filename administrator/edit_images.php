<?php
include "header.php";
if (!isset($_SESSION['admin_logged_in']) ){
    header('Location:login.php');
}
if (!isset($_GET['product_id']) ){
    header('Location:products.php');

}
?>
<div class="container-fluid">
    <div class="row" style="min-height: 1000px">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <form id="edit_images" method="post"  enctype="multipart/form-data" action="change_images.php" >

                    <div class="form-group">
                        <input type="hidden" class="form-control"  id="name" name="name"  value="<?php $_GET['product_name']?>" required>
                    </div>
                <div class="form-group">
                    <input type="hidden" class="form-control"  id="id" name="id" value="<?php $_GET['product_id']?>" required>
                </div>
                <div class="form-group">
                    <label>Image 1</label>
                    <input type="file" class="form-control"  id="img1" name="img1" placeholder="img" required>
                </div>
                <div class="form-group">
                    <label>Image 2</label>
                    <input type="file" class="form-control"  id="img2" name="img2" placeholder="img" required>
                </div>
                <div class="form-group">
                    <label>Image 3</label>
                    <input type="file" class="form-control"  id="img3" name="img3" placeholder="img" required>
                </div>
                <div class="form-group">
                    <label>Image 4</label>
                    <input type="file" class="form-control"  id="img4" name="img4" placeholder="img" required>
                </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary"  name="change-images" value="Edit">
                    </div>
            </form>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>