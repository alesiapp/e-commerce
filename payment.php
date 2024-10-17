<?php
session_start();
include('includes/connection.php');
include('layouts/header.php');

if (!isset($_SESSION['logged_in']) && !isset($_SESSION['cart'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_POST['place_order'])) {
    header('Location: checkout.php');
    exit();
}

$name = htmlspecialchars(trim($_POST['name']));
$phone = htmlspecialchars(trim($_POST['phone']));
$city =htmlspecialchars( trim($_POST['city']));
$address =htmlspecialchars(trim($_POST['address'])) ;


if (empty($name) || empty($phone) || empty($city) || empty($address)) {

    header('Location: checkout.php?error='.urlencode('Empty fields'));
    exit();
}

$_SESSION['name'] = htmlspecialchars($_POST['name']);
$_SESSION['phone'] = htmlspecialchars($_POST['phone']);
$_SESSION['city'] =htmlspecialchars($_POST['city']) ;
$_SESSION['shipping'] =htmlspecialchars($_POST['address']) ;


if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$total = isset($_SESSION['total']) ? $_SESSION['total'] : 0;
$amount = number_format($total + $_SESSION['shipping'], 2);
?>

<style>
    .paypal-container {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>

<body>

<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Payment</h2>
        <hr class="mx-auto">
    </div>
    <form id="reserve-stock-form" method="POST" action="reserve_stock.php">
        <input type="hidden" name="user_id" value="<?php echo isset($_SESSION['logged_in']) ? $_SESSION['user_id'] : ''; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    </form>
    <div class="mx-auto container text-center">
        <?php if ($amount > 0): ?>
            <p>Total amount: $<?php echo number_format($amount, 2); ?></p>
            <div class="paypal-container">

                <div id="paypal-button-container"></div>
            </div>
        <?php else: ?>
            <p>You do not have any products in your cart</p>
        <?php endif; ?>
    </div>
</section>

<script src="https://www.paypal.com/sdk/js?client-id=AYYQWdLvZJjPnkURg_kqUj5m3fqJ2pGmh-yMoCNzac-OfMAT8GDZrjWxvJlgQUXvix7-Mip1DnkwVDqL&buyer-country=US&currency=USD&components=buttons&enable-funding=venmo"></script>

<script>
    let paymentApproved = false;
    let timedout = false;
    const RESERVATION_DURATION = 100000;

    function restoreStock() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'restore_stock.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        // Send CSRF token with the request
        var csrfToken = '<?php echo isset($_SESSION["csrf_token"]) ? $_SESSION["csrf_token"] : ""; ?>';
        xhr.send('csrf_token=' + encodeURIComponent(csrfToken) + '&reason=timeout');
    }

    let reservationTimeout = setTimeout(function() {
        if (!paymentApproved) {
            timedout = true;
            restoreStock();
            window.removeEventListener('beforeunload', beforeUnloadHandler);
            window.location.href = "cart.php?message=reservation_expired";
        }
    }, RESERVATION_DURATION);

    paypal.Buttons({
        fundingSource: paypal.FUNDING.PAYPAL,
        createOrder: function(data, actions) {
            return new Promise(function(resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'reserve_stock.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        console.log(xhr.responseText); // Debug response
                        if (xhr.responseText.trim() === 'Stock reserved successfully.') {
                            resolve();
                        } else {
                            alert('Failed to reserve stock: ' + xhr.responseText);
                            reject();
                        }
                    }
                };
                var user_id = '<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>';
                xhr.send('user_id=' + encodeURIComponent(user_id));
            }).then(function() {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo $amount; ?>' // Total amount for the transaction
                        }
                    }]
                });
            }).catch(function() {
                alert('Failed to reserve stock. Try again.');
            });
        },
        onApprove: function(data, actions) {
            clearTimeout(reservationTimeout);
            return actions.order.capture().then(function(details) {
                paymentApproved = true;

                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'includes/complete_payment.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);

                        if (response.status === 'success') {
                            window.location.href = response.redirect;
                        } else {
                            window.location.href = response.redirect;
                        }
                    }
                };

                xhr.send('transaction_id=' + encodeURIComponent(details.id));
            });
        },
        onCancel: function(data) {
            paymentApproved = true;
            restoreStock();
            window.location.href = "cart.php?message=payment_cancelled";
        },
        onError: function(err) {
            paymentApproved = true;
            restoreStock();
            window.location.href = "cart.php?message=payment_error";
        }
    }).render('#paypal-button-container');

    function beforeUnloadHandler(event) {
        if (!paymentApproved && !timedout) {
            var confirmationMessage = 'Are you sure you want to leave? Any unsaved changes will be lost.';
            event.returnValue = confirmationMessage;
            return confirmationMessage;
        }
    }

    window.addEventListener('beforeunload', beforeUnloadHandler);

    window.addEventListener('unload', function() {
        if (!paymentApproved && !timedout) {
            restoreStock();
        }
    });
</script>

<!-- PayPal button container -->
<div id="paypal-button-container"></div>

<?php include('layouts/footer.php'); ?>
