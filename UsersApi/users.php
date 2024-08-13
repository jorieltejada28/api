<?php
    include_once('../connection/config.php');

    class UserAPI {
        private $conn;
        private $table_name = "customers";

        public function __construct($db) {
            $this->conn = $db;
        }

        public function getUsers() {
            // Count the number of rows in the table
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the table is empty
            if ($row['total'] == 0) {
                return null;
            }

            // If not empty, fetch all users
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        public function getUser($id) {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            return $stmt;
        }

        public function createUser($name, $email, $phone) {
            $query = "INSERT INTO " . $this->table_name . " (name, email, phone) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $name);
            $stmt->bindParam(2, $email);
            $stmt->bindParam(3, $phone);
            return $stmt->execute();  
        }

        public function updateUser($id, $name, $email, $phone) {
            $query = "UPDATE " . $this->table_name . " SET name = ?, email = ?, phone = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $name);
            $stmt->bindParam(2, $email);
            $stmt->bindParam(3, $phone);
            $stmt->bindParam(4, $id);
            return $stmt->execute();
        }

        public function deleteUser($id) {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            return $stmt->execute();
        }
    }
?>
