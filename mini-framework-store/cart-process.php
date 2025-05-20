<?php
session_start();
require 'vendor/autoload.php';

use Rasheed\MiniFrameworkStore\Models\Product;

header('Content-Type: application/json');

if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

$action = $_POST['action'];
$product = new Product();

switch ($action) {
    case 'add':
        if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        $productId = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }

        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['cart']),
            'message' => 'Product added to cart'
        ]);
        break;

    case 'update':
        if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        $productId = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        if ($quantity > 0) {
            $_SESSION['cart'][$productId] = $quantity;
        } else {
            unset($_SESSION['cart'][$productId]);
        }

        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['cart']),
            'message' => 'Cart updated'
        ]);
        break;

    case 'remove':
        if (!isset($_POST['product_id'])) {
            echo json_encode(['success' => false, 'message' => 'Missing product ID']);
            exit;
        }

        $productId = $_POST['product_id'];
        unset($_SESSION['cart'][$productId]);

        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['cart']),
            'message' => 'Product removed from cart'
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

?>