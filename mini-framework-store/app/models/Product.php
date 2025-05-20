<?php

namespace Rasheed\MiniFrameworkStore\Models;

use Rasheed\MiniFrameworkStore\Includes\Database;

class Product extends Database
{
    private $db;

    public function __construct()
    {
        parent::__construct(); // Call the parent constructor to establish the connection
        $this->db = $this->getConnection(); // Get the connection instance
    }

    public function insert($data)
    {
        $sql = "INSERT INTO products (name, description, price, slug, image_path, category_id, created_at, updated_at) VALUES (:name, :description, :price, :slug, :image_path, :category_id, :created_at, :updated_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'slug' => $data['slug'],
            'image_path' => $data['image_path'],
            'category_id' => $data['category_id'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['updated_at']
        ]);
        return $this->db->lastInsertId();
    }
    public function update()
    {
        $sql = "UPDATE products SET name = :name, description = :description, price = :price, image_path = :image_path WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'image_path' => $this->image_path
        ]);
        return "Record UPDATED successfully";
    }
    public function delete()
    {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $this->id
        ]);
        return "Record DELETED successfully";
    }
    public function getAll()
    {
        $sql = "SELECT * FROM products";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getById($id)
    {
        $sql = "SELECT * FROM products WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function getByName($name)
    {
        $sql = "SELECT * FROM products WHERE name = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $name
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function getByPrice($price)
    {
        $sql = "SELECT * FROM products WHERE price = :price";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'price' => $price
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getByCategory($category_id)
    {
        $sql = "SELECT products.id, products.name, products.price, products.description, products.image_path FROM products JOIN product_categories ON products.category_id = product_categories.id WHERE product_categories.id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'category_id' => $category_id
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}