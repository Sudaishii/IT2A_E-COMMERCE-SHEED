<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

use Rasheed\MiniFrameworkStore\Models\Product;
use Rasheed\MiniFrameworkStore\Models\Checkout;

// Check if user is logged in, otherwise redirect to login page
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Ensure cart is not empty for logged in users trying to checkout
if (empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products = new Product();
$checkout = new Checkout();
$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);

$total = 0;
$cartItems = [];

foreach ($cart as $productId => $quantity) {
    $product = $products->getById($productId);
    if ($product) {
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        $cartItems[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'image' => $product['image_path']
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the order for logged-in users
    $customerId = $_SESSION['user']['id'];
    // Use the shipping address from the form, or potentially default from user profile
    $shippingAddress = $_POST['shipping_address'] ?? $_SESSION['user']['address'] ?? null;

    // Create order data array
    $orderData = [
        'customer_id' => $customerId,
        'guest_name' => null, // No guest name for logged-in users
        'guest_phone' => null, // No guest phone for logged-in users
        'guest_address' => null, // No guest address for logged-in users
        'total' => $total
        // Note: Address is part of the user's profile, not stored directly in the order for logged-in users in this model structure.
        // If you need to store the specific shipping address for this order, the 'orders' table or 'order_details' table structure needs adjustment.
    ];

    // Save order to database using the Checkout model (only userCheckout needed now)
    $orderId = $checkout->userCheckout($orderData);

    if ($orderId) {
        // Save order details using the Checkout model
        foreach ($cartItems as $item) {
            $orderDetailData = [
                'order_id' => $orderId,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal']
            ];
            $checkout->saveOrderDetails($orderDetailData);
        }

        // Clear cart
        unset($_SESSION['cart']);

        // Redirect to success page
        header('Location: order-success.php?id=' . $orderId);
        exit;
    }
}
?>

<div class="container my-5">
    <h1 class="mb-4">Checkout</h1>

    <?php if (empty($cartItems)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Your cart is empty. 
            <a href="index.php" class="alert-link">Continue shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Checkout Form -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-olive text-white">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="checkout.php">
                            <div class="mb-3">
                                <label for="shipping_address" class="form-label">Shipping Address</label>
                                <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required><?php echo $_SESSION['user']['address']; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                    <label class="form-check-label" for="cod">
                                        Cash on Delivery (COD)
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Place Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-olive text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
                                <span><?php echo $pesoFormatter->formatCurrency($item['subtotal'], 'PHP'); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span><?php echo $pesoFormatter->formatCurrency($total, 'PHP'); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-success"><?php echo $pesoFormatter->formatCurrency($total, 'PHP'); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php template('footer.php'); ?>