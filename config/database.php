<?php
// Database configuration for AwardSpace
class Database {
    private $host = 'fdb1034.awardspace.net'; // Usually localhost for AwardSpace
    private $db_name = '4671383_act2cslec1';
    private $username = '4671383_act2cslec1'; // Replace with your AwardSpace MySQL username
    private $password = 'AMhJ0l1U5([pNLQU'; // Replace with your AwardSpace MySQL password
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
