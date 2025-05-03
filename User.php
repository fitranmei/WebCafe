<?php
require_once "Database.php";

class User {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getUserById($idUser) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE idUser = ?");
        $stmt->bind_param("i", $idUser);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createUser($fullName, $userName, $email, $password, $role, $profilePicture) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare(
            "INSERT INTO users (fullName, userName, email, password, role, profilePicture) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssss", $fullName, $userName, $email, $hashedPassword, $role, $profilePicture);
        return $stmt->execute();
    }

    public function updateUser($idUser, $fullName, $userName, $email, $role, $profilePicture) {
        $stmt = $this->conn->prepare(
            "UPDATE users SET fullName = ?, userName = ?, email = ?, role = ?, profilePicture = ? WHERE idUser = ?"
        );
        $stmt->bind_param("sssssi", $fullName, $userName, $email, $role, $profilePicture, $idUser);
        return $stmt->execute();
    }

    public function updatePassword($idUser, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE idUser = ?");
        $stmt->bind_param("si", $hashedPassword, $idUser);
        return $stmt->execute();
    }

    public function deleteUser($idUser) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE idUser = ?");
        $stmt->bind_param("i", $idUser);
        return $stmt->execute();
    }

    public function authenticate($emailOrUsername, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? OR userName = ?");
        $stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function isEmailOrUsernameExists($email, $username) {
        $stmt = $this->conn->prepare("SELECT idUser FROM users WHERE email = ? OR userName = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function loginUser($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                unset($user['password']); 
                return $user;
            }
        }
    
        return false;
    }
    
}
?>
