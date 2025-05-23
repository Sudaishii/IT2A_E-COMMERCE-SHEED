<?php include 'helpers/functions.php'; ?>
<?php

// Check if admin is logged in
if (!isset($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true) {
    // Redirect to admin login page if not logged in as admin
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
            // Fetch all orders using the modified getAllOrders method
            $allOrders = $orders->getAllOrders();

            foreach ($allOrders as $order) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($order['id']) . '</td>';
                // Display user name if available, otherwise guest name
                echo '<td>' . htmlspecialchars($order['user_name'] ?? $order['guest_name']) . '</td>';
                echo '<td>' . htmlspecialchars($order['total']) . '</td>';
                echo '<td>' . htmlspecialchars(date('F j, Y H:i', strtotime($order['created_at']))) . '</td>'; // Display full date and time
                // Add a link to view order details (using order-success.php for now)
                echo '<td><a href="order-success.php?id=' . $order['id'] . '" class="btn btn-sm btn-info">View Details</a></td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php template('footer.php'); ?>