<?php

namespace Rasheed\MiniFrameworkStore\Models;

use Rasheed\MiniFrameworkStore\Includes\Database;

class Order extends Database
{
    private $db;

    public function __construct()
    {
        parent::__construct(); // Call the parent constructor
        $this->db = $this->getConnection(); // Get the connection
    }

    /**
     * Get all orders for a specific user.
     *
     * @param int $userId The ID of the user.
     * @return array An array of order records.
     */
    public function getOrdersByUserId($userId)
    {
        $sql = "SELECT * FROM orders WHERE customer_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get the details (items) for a specific order.
     *
     * @param int $orderId The ID of the order.
     * @return array An array of order detail records.
     */
    public function getOrderDetailsByOrderId($orderId)
    {
        $sql = "SELECT od.*, p.name, p.image_path FROM order_details od JOIN products p ON od.product_id = p.id WHERE od.order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'order_id' => $orderId
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get a single order by its ID.
     *
     * @param int $orderId The ID of the order.
     * @return array|false An associative array of the order record, or false if not found.
     */
    public function getOrderById($orderId)
    {
        $sql = "SELECT * FROM orders WHERE id = :order_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'order_id' => $orderId
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // You might also want methods for:
    // - Creating a new order
    // - Updating order status, etc.

    /**
     * Get all orders in the system (for admin).
     *
     * @return array An array of all order records.
     */
    public function getAllOrders()
    {
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
