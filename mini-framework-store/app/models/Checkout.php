<?php

namespace Rasheed\MiniFrameworkStore\Models;

use Rasheed\MiniFrameworkStore\Includes\Database;
use Carbon\Carbon;

class Checkout extends Database
{

    private $db;

    public function __construct()
    {
        parent::__construct(); // Call the parent constructor to establish the connection
        $this->db = $this->getConnection(); // Get the connection instance
    }

    public function guestCheckout($data)
    {
        $sql = "INSERT INTO orders (customer_id, guest_name, guest_phone, guest_address, total, created_at, updated_at) VALUES (:customer_id, :guest_name, :guest_phone, :guest_address, :total, :created_at, :updated_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'customer_id' => null,
            'guest_name' => $data['name'],
            'guest_phone' => $data['phone'],
            'guest_address' => $data['address'],
            'total' => $data['total'],
            'created_at' => Carbon::now('Asia/Manila'),
            'updated_at' => Carbon::now('Asia/Manila')
        ]);

        return $this->db->lastInsertId();
    }

    public function userCheckout($data)
    {
        $sql = "INSERT INTO orders (customer_id, landmark_address, total, created_at, updated_at) VALUES (:customer_id, :landmark_address, :total, :created_at, :updated_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'customer_id' => $data['customer_id'],
            'landmark_address' => $data['landmark_address'] ?? null,
            'total' => $data['total'],
            'created_at' => Carbon::now('Asia/Manila'),
            'updated_at' => Carbon::now('Asia/Manila')
        ]);

        return $this->db->lastInsertId();
    }

    public function saveOrderDetails($data)
    {
        $sql = "INSERT INTO order_details (order_id, product_id, quantity, price, subtotal) VALUES (:order_id, :product_id, :quantity, :price, :subtotal)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'order_id' => $data['order_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
            'subtotal' => $data['subtotal']
        ]);
    }

    public function getAllOrders()
    {
        // Modified query to select distinct orders, include user or guest name, and order by created_at DESC
        $sql = "SELECT
                    o.id,
                    o.customer_id,
                    u.name AS user_name,
                    o.guest_name,
                    o.total,
                    o.created_at
                FROM
                    orders o
                LEFT JOIN
                    users u ON o.customer_id = u.id
                ORDER BY
                    o.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}