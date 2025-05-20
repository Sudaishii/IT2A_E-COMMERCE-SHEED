<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'vendor/autoload.php';

function assets($path) {
    include 'assets/' . $path;
}

function template($path) {
    include 'templates/' . $path;
}

function countCart() {
    if (isset($_SESSION['cart'])) {
        return count($_SESSION['cart']);
    }
    return 0;
}

function isLoggedIn() {
    if(isset($_SESSION['user'])) {
        return true;
    }

    return false;
}