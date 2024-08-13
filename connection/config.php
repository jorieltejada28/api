<?php
    class Database {
        private $host = "Localhost: 3307";
        private $dbname = "api";
        private $username = "root";
        private $password = "";
        private $conn;

        public function getConnection() {
            $this->conn = null;

            try {
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $exception) {
                echo "Connection Error: " . $exception->getMessage();
            }

            return $this->conn;
        }
    }
?>
