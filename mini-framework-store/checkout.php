<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

use Rasheed\MiniFrameworkStore\Models\Product;

if (!isLoggedIn() && empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products = new Product();
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
    // Process the order
    $customerId = isLoggedIn() ? $_SESSION['user']['id'] : null;
    $guestName = !isLoggedIn() ? $_POST['name'] : null;
    $guestPhone = !isLoggedIn() ? $_POST['phone'] : null;
    $guestAddress = !isLoggedIn() ? $_POST['address'] : null;
    $shippingAddress = isLoggedIn() ? $_POST['shipping_address'] : $guestAddress;

    // Create order
    $order = [
        'customer_id' => $customerId,
        'guest_name' => $guestName,
        'guest_phone' => $guestPhone,
        'guest_address' => $guestAddress,
        'total' => $total,
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Save order to database
    $orderId = saveOrder($order);

    if ($orderId) {
        // Save order details
        foreach ($cartItems as $item) {
            $orderDetail = [
                'order_id' => $orderId,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal']
            ];
            saveOrderDetail($orderDetail);
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
                            <?php if (!isLoggedIn()): ?>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <label for="shipping_address" class="form-label">Shipping Address</label>
                                    <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required><?php echo $_SESSION['user']['address']; ?></textarea>
                                </div>
                            <?php endif; ?>

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