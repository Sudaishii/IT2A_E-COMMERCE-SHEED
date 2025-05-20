<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

use Rasheed\MiniFrameworkStore\Models\User;
use Rasheed\MiniFrameworkStore\Models\Order;
use Rasheed\MiniFrameworkStore\Models\Product; // Include Product model if needed elsewhere
use Carbon\Carbon; // Assuming you use Carbon for dates

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];

// Instantiate the Order model
$orderModel = new Order();

// Fetch user's orders using the Order model
$orders = $orderModel->getOrdersByUserId($user['id']);

// Instantiate Product model for fetching product details within order details
$productModel = new Product();

$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $address = $_POST['address'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $birthdate = $_POST['birthdate'] ?? null;

    // Update user details in the database
    $userModel = new User();
    $userModel->update([
        'id' => $_SESSION['user']['id'],
        'name' => $name,
        'email' => $_SESSION['user']['email'],
        'address' => $address,
        'phone' => $phone,
        // Use Carbon to format date if birthdate is not null and is a valid date string
        'birthdate' => ($birthdate && strtotime($birthdate)) ? Carbon::createFromFormat('Y-m-d', $birthdate)->format('Y-m-d') : null
    ]);

    // Update session data
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['address'] = $address;
    $_SESSION['user']['phone'] = $phone;
    $_SESSION['user']['birthdate'] = $birthdate;

    echo "<script>alert('Account details updated successfully!');</script>";
}

?>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-olive text-white">
                    <h5 class="mb-0">My Account</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                        <i class="bi bi-person"></i> Profile
                    </a>
                    <a href="#orders" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-bag"></i> Orders
                    </a>
                    <a href="#settings" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Profile Tab -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="card">
                        <div class="card-header bg-olive text-white">
                            <h5 class="mb-0">Profile Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="my-account.php" method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" value="<?php echo $user['email']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="birthdate" class="form-label">Birthdate</label>
                                        <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo $user['birthdate']; ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $user['address']; ?></textarea>
                                </div>
                                <button type="submit" name="submit" class="btn btn-success">
                                    <i class="bi bi-save"></i> Update Profile
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Orders Tab -->
                <div class="tab-pane fade" id="orders">
                    <div class="card">
                        <div class="card-header bg-olive text-white">
                            <h5 class="mb-0">Order History</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($orders)): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> You haven't placed any orders yet.
                                </div>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <div class="card mb-3">
                                        <div class="card-header d-flex justify-content-between align-items-center">
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
                                        <div class="card-body">
                                            <?php
                                            // Fetch order details for this order using the Order model
                                            $orderDetails = $orderModel->getOrderDetailsByOrderId($order['id']);
                                            foreach ($orderDetails as $detail):
                                                // Product details are already joined in getOrderDetailsByOrderId
                                                // No need to call getProductById here
                                            ?>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo $detail['name']; ?></h6>
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
                    </div>
                </div>

                <!-- Settings Tab -->
                <div class="tab-pane fade" id="settings">
                    <div class="card">
                        <div class="card-header bg-olive text-white">
                            <h5 class="mb-0">Account Settings</h5>
                        </div>
                        <div class="card-body">
                            <form action="update-password.php" method="POST">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-key"></i> Update Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>