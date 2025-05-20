<?php

namespace Rasheed\MiniFrameworkStore\Models;

use Rasheed\MiniFrameworkStore\Includes\Database;


class User extends Database {
    private $db;

    public function __construct() {
        parent::__construct(); // Call the parent constructor to establish the connection
        $this->db = $this->getConnection(); // Get the connection instance
    }

    public function login($data) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'email' => $data['email'],
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function register($data) {
        $sql = "INSERT INTO users (name, email, password, address, phone, birthdate, created_at, updated_at) VALUES (:name, :email, :password, :address, :phone, :birthdate, :created_at, :updated_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'address' => $data['address'],
            'phone' => $data['phone'],
            'birthdate' => $data['birthdate'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['updated_at']
        ]);

        return $this->db->lastInsertId();
    }

    public function update($data) {
        $sql = "UPDATE users SET name = :name, email = :email, address = :address, phone = :phone, birthdate = :birthdate WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $data['id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'birthdate' => $data['birthdate']
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id
        ]);
    }   
}