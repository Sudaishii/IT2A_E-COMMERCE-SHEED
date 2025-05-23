<?php
include 'helpers/functions.php';
template('header.php');

use Rasheed\MiniFrameworkStore\Models\Order;
use Rasheed\MiniFrameworkStore\Models\Product;

if (!isLoggedIn() || !isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header('Location: index.php');
    exit;
}

$orderModel = new Order();
$orders = $orderModel->getAllOrders();

$productModel = new Product();

$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);
?>

<div class="container my-5">
    <h2>All Orders</h2>
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No orders found.
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <a href="order-success.php?id=<?php echo $order['id']; ?>" style="text-decoration: none; color: inherit; width: 100%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Order #<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                                </small>
                            </div>
                            <span class="badge bg-success">
                                <?php echo $pesoFormatter->formatCurrency($order['total'], 'PHP'); ?>
                            </span>
                        </div>
                    </a>
                </div>
                <div class="card-body">
                    <?php
                    $orderDetails = $orderModel->getOrderDetailsByOrderId($order['id']);
                    foreach ($orderDetails as $detail):
                    ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($detail['name']); ?></h6>
                                <small class="text-muted">
                                    Quantity: <?php echo $detail['quantity']; ?> x 
                                    <?php echo $pesoFormatter->formatCurrency($detail['price'], 'PHP'); ?>
                                </small>
                            </div>
                            <span>
                                <?php echo $pesoFormatter->formatCurrency($detail['subtotal'], 'PHP'); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php template('footer.php'); ?>
