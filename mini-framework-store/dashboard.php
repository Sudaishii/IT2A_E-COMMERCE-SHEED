<?php include 'helpers/functions.php'; ?>
<?php


if (!isset($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true) {
    
    header('Location: admin_login.php');
    exit;
}

use Rasheed\MiniFrameworkStore\Models\Checkout;
$orders = new Checkout();

?>

<?php template('header.php'); ?>

<div class="container my-5">
    <h2>All Orders</h2>
    <p>Here are all orders placed on the site, ordered by latest:</p>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Total</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
       
            $allOrders = $orders->getAllOrders();

            foreach ($allOrders as $order) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($order['id']) . '</td>';
               
                echo '<td>' . htmlspecialchars($order['user_name'] ?? $order['guest_name']) . '</td>';
                echo '<td>' . htmlspecialchars($order['total']) . '</td>';
                echo '<td>' . htmlspecialchars(date('F j, Y H:i', strtotime($order['created_at']))) . '</td>';
               
                echo '<td><a href="order-success.php?id=' . $order['id'] . '" class="btn btn-sm btn-info">View Details</a></td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php template('footer.php'); ?>