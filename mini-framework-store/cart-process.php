<?php
session_start();
require 'vendor/autoload.php';

use Rasheed\MiniFrameworkStore\Models\Product;

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);

// Debug session
error_log('Session data: ' . print_r($_SESSION, true));
error_log('POST data: ' . print_r($_POST, true));

// Check if user is logged in for cart operations
if (!isset($_SESSION['user'])) {
    ob_clean(); // Clear output buffer
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart', 'redirect' => 'login.php']);
    exit;
}

if (!isset($_POST['action'])) {
    ob_clean(); // Clear output buffer
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

$action = $_POST['action'];
$product = new Product();

switch ($action) {
    case 'add':
        if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
            ob_clean(); // Clear output buffer
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        $productId = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        // Debug input
        error_log('Adding to cart - Product ID: ' . $productId . ', Quantity: ' . $quantity);

        // Validate quantity
        if ($quantity < 1 || $quantity > 99) {
            ob_clean(); // Clear output buffer
            echo json_encode(['success' => false, 'message' => 'Invalid quantity. Must be between 1 and 99']);
            exit;
        }

        // Get product details to check stock
        $productDetails = $product->getById($productId);
        if (!$productDetails) {
            ob_clean(); // Clear output buffer
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if product already exists in cart
        if (isset($_SESSION['cart'][$productId])) {
            $newQuantity = $_SESSION['cart'][$productId] + $quantity;
            // Check if new total quantity exceeds limit
            if ($newQuantity > 99) {
                ob_clean(); // Clear output buffer
                echo json_encode(['success' => false, 'message' => 'Maximum quantity limit reached']);
                exit;
            }
            $_SESSION['cart'][$productId] = $newQuantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }

        // Calculate total items in cart
        $totalItems = array_sum($_SESSION['cart']);

        // Log session data for debugging
        error_log('Session cart after update: ' . print_r($_SESSION['cart'], true));
        error_log('Total items: ' . $totalItems);

        ob_clean(); // Clear output buffer
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['cart']),
            'total_items' => $totalItems,
            'message' => 'Product added to cart'
        ]);
        break;

    case 'update':
        if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
            ob_clean(); // Clear output buffer
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        $productId = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        // Validate quantity
        if ($quantity < 0 || $quantity > 99) {
            ob_clean(); // Clear output buffer
            echo json_encode(['success' => false, 'message' => 'Invalid quantity. Must be between 0 and 99']);
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if ($quantity > 0) {
            $_SESSION['cart'][$productId] = $quantity;
        } else {
            unset($_SESSION['cart'][$productId]);
        }

        // Recalculate total items and total price
        $totalItems = array_sum($_SESSION['cart']);
        $totalPrice = 0;
        $product = new Product(); // Re-instantiate or ensure Product model is available
        $updatedItemSubtotal = 0;

        foreach ($_SESSION['cart'] as $id => $qty) {
            $productDetails = $product->getById($id);
            if ($productDetails) {
                 if ($id == $productId) { // Get the updated subtotal for the current item
                    $updatedItemSubtotal = $productDetails['price'] * $qty;
                 }
                $totalPrice += $productDetails['price'] * $qty;
            }
        }

        ob_clean(); // Clear output buffer
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['cart']),
            'total_items' => $totalItems,
            'updated_item_subtotal' => $updatedItemSubtotal, // Return updated subtotal for the item
            'total_price' => $totalPrice, // Return new overall total price
            'message' => 'Cart updated'
        ]);
        break;

    case 'remove':
        if (!isset($_POST['product_id'])) {
            ob_clean(); // Clear output buffer
            echo json_encode(['success' => false, 'message' => 'Missing product ID']);
            exit;
        }

        $productId = $_POST['product_id'];

        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }

        // Recalculate total items and total price after removal
        $totalItems = array_sum($_SESSION['cart']);
        $totalPrice = 0;
        $product = new Product(); // Re-instantiate or ensure Product model is available

        foreach ($_SESSION['cart'] as $id => $qty) {
            $productDetails = $product->getById($id);
            if ($productDetails) {
                $totalPrice += $productDetails['price'] * $qty;
            }
        }

        ob_clean(); // Clear output buffer
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['cart']),
            'total_items' => $totalItems,
            'total_price' => $totalPrice, // Return new overall total price
            'message' => 'Product removed from cart'
        ]);
        break;

    default:
        ob_clean(); // Clear output buffer
        echo json_encode((array)[
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
}
?>