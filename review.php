<?php
include ('layouts/header.php');
include('includes/connection.php');

if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $stmt2 = $conn->prepare('SELECT COUNT(*) FROM reviews WHERE id_porosi = ?');
    $stmt2->bind_param('i', $order_id);
    $stmt2->execute();
    $stmt2->bind_result($review_count);
    $stmt2->fetch();
    $stmt2->close();


    if ($review_count > 0) {

        header('Location: account.php?error='. urlencode('You have already submitted reviews for this order'));
        exit();
    }

    $stmt = $conn->prepare( 'Select id_porosi,p.emri,p.foto1,p.id,p.cmimi,o.sasi_produkti from produkte_porosi o join produkte p on o.id_produkti=p.id  where o.id_porosi=?');
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order_details = $stmt->get_result();
} else {
    header('location:account.php');
    exit;
}
?>






<section id="review-section" class="reviews container my-5 py-3">
    <div class="container text-center mt-5">
        <h2 class="font-weight-bold">Review Products</h2>
        <hr class="mx-auto" style="width: 50px;">
    </div>

    <form method="post" action="submit_review.php" class="mt-4">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

        <?php while ($row = $order_details->fetch_assoc()) { ?>
            <div class="review-item my-4">
                <div class="row align-items-center">
                    <!-- Product Image -->
                    <div class="col-md-2 col-sm-12 text-center">
                        <img src="imgs/<?php echo $row['foto1']; ?>" class="product-img img-fluid" alt="<?php echo $row['emri']; ?>" style="max-width: 100px;">
                    </div>

                    <div class="col-md-10 col-sm-12">
                        <p class="font-weight-bold"><?php echo $row['emri']; ?></p>

                        <textarea name="reviews[<?php echo $row['id']; ?>]" class="form-control mb-3" rows="3" placeholder="Write your review here..."></textarea>

                        <div class="rating">
                            <label for="rating-<?php echo $row['id']; ?>">Rating:</label>
                            <select name="ratings[<?php echo $row['id']; ?>]" id="rating-<?php echo $row['id']; ?>" class="form-select w-auto d-inline-block">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Submit Button -->
        <div class="text-center">
            <input type="submit" name="submit_review_btn" value="Submit Reviews" class="btn btn-primary mt-4 px-5">
        </div>
    </form>
</section>



<script>
    document.getElementById('review-form').addEventListener('submit', function(event) {
        // Get all review items
        const reviewItems = document.querySelectorAll('.review-item');
        let valid = true;

        reviewItems.forEach(item => {
            const textarea = item.querySelector('.review-textarea');
            const ratingSelect = item.querySelector('.rating-select');
            const errorContainer = document.createElement('p');
            errorContainer.style.color = 'red';
            errorContainer.classList.add('error-message');

            if (textarea.value === '') {
                errorContainer.textContent = 'Please enter a review.';
                if (!item.querySelector('.error-message')) {
                    item.appendChild(errorContainer);
                }
                valid = false;
            } else {

                const existingError = item.querySelector('.error-message');
                if (existingError) existingError.remove();
            }

            if (ratingSelect.value === '') {
                errorContainer.textContent = 'Please select a rating.';
                if (!item.querySelector('.error-message')) {
                    item.appendChild(errorContainer);
                }
                valid = false;
            } else {

                const existingError = item.querySelector('.error-message');
                if (existingError) existingError.remove();
            }
        });

        if (!valid) {
            event.preventDefault();
        }
    });
</script>
<?php include('layouts/footer.php'); ?>
