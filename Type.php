<?php
require_once "Database.php";

class Type {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAllTypes() {
        $result = $this->conn->query("SELECT * FROM types");
        $types = [];

        while ($row = $result->fetch_assoc()) {
            $types[] = $row;
        }

        return $types;
    }

    public function getTypeById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM types WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
