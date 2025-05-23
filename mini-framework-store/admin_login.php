<?php
session_start();

// Define admin credentials (Replace with secure method in production)
$adminEmail = 'admin@yourstore.com'; // Replace with your desired admin email
$adminPassword = 'admin123'; // Replace with a strong password

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Simple check against hardcoded credentials
    if ($email === $adminEmail && $password === $adminPassword) {
        // Set admin session variable
        $_SESSION['is_admin_logged_in'] = true;
        // Redirect to dashboard
        header('Location: dashboard.php');
        exit;
    } else {
        $errorMessage = 'Invalid admin email or password.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .admin-login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="admin-login-container">
        <h2 class="text-center mb-4">Admin Login</h2>
        
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin_login.php">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 