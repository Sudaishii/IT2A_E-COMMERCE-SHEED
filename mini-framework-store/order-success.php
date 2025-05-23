<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>

<?php
use Rasheed\MiniFrameworkStore\Models\Order; 

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$orderId = $_GET['id'];

$orderModel = new Order(); 
$order = $orderModel->getOrderById($orderId); 

if (!$order) {
    header('Location: index.php');
    exit;
}

$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);


$orderDetails = $orderModel->getOrderDetailsByOrderId($orderId)

?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h1 class="mb-4">Order Placed Successfully!</h1>
                    <p class="lead mb-4">Thank you for your purchase. Your order has been received.</p>
                    
                    <div class="order-details mb-4">
                        <h5>Order Details</h5>
                        <p class="mb-1">Order Number: #<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></p>
                        <p class="mb-1">Date: <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                        <p class="mb-1">Total: <?php echo $pesoFormatter->formatCurrency($order['total'], 'PHP'); ?></p>
                        <p class="mb-1">Payment Method: Cash on Delivery</p>
                        <ul class="list-group text-start">
                        <?php foreach ($orderDetails as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><?php echo htmlspecialchars($item['name']); ?></span>
                                <span>Qty: <?php echo $item['quantity']; ?></span>
                                <span>₱<?php echo number_format($item['subtotal'], 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    </div>

                    <div class="shipping-details mb-4">
                        <h5>Shipping Information</h5>
                        <?php if ($order['customer_id']): ?>
                            <p class="mb-1">Name: <?php echo $_SESSION['user']['name']; ?></p>
                            <p class="mb-1">Phone: <?php echo $_SESSION['user']['phone']; ?></p>
                            <p class="mb-1">Address: <?php echo $_SESSION['user']['address']; ?></p>
                        <?php else: ?>
                            <p class="mb-1">Name: <?php echo $order['guest_name']; ?></p>
                            <p class="mb-1">Phone: <?php echo $order['guest_phone']; ?></p>
                            <p class="mb-1">Address: <?php echo $order['guest_address']; ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="index.php" class="btn btn-primary">
                            <i class="bi bi-house"></i> Continue Shopping
                        </a>
                        <?php if (isLoggedIn()): ?>
                            <a href="my-account.php" class="btn btn-success">
                                <i class="bi bi-person"></i> View My Orders
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php template('footer.php'); ?> 