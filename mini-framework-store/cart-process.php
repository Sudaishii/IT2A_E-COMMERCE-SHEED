<?php
session_start();
require 'vendor/autoload.php';

use Rasheed\MiniFrameworkStore\Models\Product;

header('Content-Type: application/json');


error_reporting(E_ALL);


error_log('Session data: ' . print_r($_SESSION, true));
error_log('POST data: ' . print_r($_POST, true));


if (!isset($_SESSION['user'])) {
    ob_clean(); 
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart', 'redirect' => 'login.php']);
    exit;
}

if (!isset($_POST['action'])) {
    ob_clean(); 
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

$action = $_POST['action'];
$product = new Product();

switch ($action) {
    case 'add':
        if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
            ob_clean(); 
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        $productId = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        
        $currentTime = microtime(true);
        if (isset($_SESSION['last_add']) &&
            $_SESSION['last_add']['product_id'] === $productId &&
            $_SESSION['last_add']['quantity'] === $quantity &&
            ($currentTime - $_SESSION['last_add']['time']) < 1.0) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Duplicate add request ignored']);
            exit;
        }
        $_SESSION['last_add'] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'time' => $currentTime
        ];

   
        error_log('Adding to cart - Product ID: ' . $productId . ', Quantity: ' . $quantity);

 
        if ($quantity < 1 || $quantity > 99) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Invalid quantity. Must be between 1 and 99']);
            exit;
        }

     
        $productDetails = $product->getById($productId);
        if (!$productDetails) {
            ob_clean(); 
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

      
        if (isset($_SESSION['cart'][$productId])) {
            $newQuantity = $_SESSION['cart'][$productId] + $quantity;
     
            if ($newQuantity > 99) {
                ob_clean(); 
                echo json_encode(['success' => false, 'message' => 'Maximum quantity limit reached']);
                exit;
            }
            $_SESSION['cart'][$productId] = $newQuantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }

   
        $totalItems = array_sum($_SESSION['cart']);

   
        error_log('Session cart after update: ' . print_r($_SESSION['cart'], true));
        error_log('Total items: ' . $totalItems);

        ob_clean(); 
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['cart']),
            'total_items' => $totalItems,
            'message' => 'Product added to cart'
        ]);
        break;

    case 'update':
        if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
            ob_clean(); 
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        $productId = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

     
        if ($quantity < 0 || $quantity > 99) {
            ob_clean(); 
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

  
        $totalItems = array_sum($_SESSION['cart']);
        $totalPrice = 0;
        $product = new Product(); 
        $updatedItemSubtotal = 0;

        foreach ($_SESSION['cart'] as $id => $qty) {
            $productDetails = $product->getById($id);
            if ($productDetails) {
                 if ($id == $productId) { 
                    $updatedItemSubtotal = $productDetails['price'] * $qty;
                 }
                $totalPrice += $productDetails['price'] * $qty;
            }
        }

        ob_clean(); 
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['cart']),
            'total_items' => $totalItems,
            'updated_item_subtotal' => $updatedItemSubtotal, 
            'total_price' => $totalPrice, 
            'message' => 'Cart updated'
        ]);
        break;

    case 'remove':
        if (!isset($_POST['product_id'])) {
            ob_clean(); 
            echo json_encode(['success' => false, 'message' => 'Missing product ID']);
            exit;
        }

        $productId = $_POST['product_id'];

        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }

        
        $totalItems = array_sum($_SESSION['cart']);
        $totalPrice = 0;
        $product = new Product(); 

        foreach ($_SESSION['cart'] as $id => $qty) {
            $productDetails = $product->getById($id);
            if ($productDetails) {
                $totalPrice += $productDetails['price'] * $qty;
            }
        }

        ob_clean(); 
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['cart']),
            'total_items' => $totalItems,
            'total_price' => $totalPrice, 
            'message' => 'Product removed from cart'
        ]);
        break;

    default:
        ob_clean(); 
        echo json_encode((array)[
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
}
?>