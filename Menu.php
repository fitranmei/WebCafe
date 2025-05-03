<?php
require_once "Database.php";

class Menu {
    private $conn;
    public $error = '';
    public $message = '';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    private function isDuplicateName($name) {
        $stmt = $this->conn->prepare("SELECT id_menu FROM menu WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->store_result();
        $isDuplicate = $stmt->num_rows > 0;
        $stmt->close();
        return $isDuplicate;
    }

    private function uploadImage($file) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_file_size = 5 * 1024 * 1024;

        if ($file['error'] !== 0) {
            $this->error = "Silakan pilih gambar";
            return null;
        }

        if (!in_array($file['type'], $allowed_types)) {
            $this->error = "Tipe file tidak diizinkan.";
            return null;
        }

        if ($file['size'] > $max_file_size) {
            $this->error = "Ukuran file terlalu besar.";
            return null;
        }

        if (!file_exists('images')) {
            mkdir('images', 0777, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $image_name = uniqid('menu_') . '.' . $ext;
        $upload_path = 'images/menu/' . $image_name;

        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            $this->error = "Gagal mengupload gambar.";
            return null;
        }

        return $image_name;
    }

    public function addMenu($name, $type, $price, $description, $imageFile) {
        $name = trim($name);
        $type = trim($type);
        $price = (int) $price;
        $description = trim($description);

        if (empty($name)) {
            $this->error = "Nama menu tidak boleh kosong";
            return false;
        }

        if ($this->isDuplicateName($name)) {
            $this->error = "Pendaftaran gagal: Nama Menu telah ada";
            return false;
        }

        if (empty($type)) {
            $this->error = "Tipe menu tidak boleh kosong";
            return false;
        }

        if ($price <= 0) {
            $this->error = "Harga harus lebih dari 0";
            return false;
        }

        $image = $this->uploadImage($imageFile);
        if (!$image) {
            return false; 
        }

        $stmt = $this->conn->prepare("INSERT INTO menu (name, type, price, image, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $name, $type, $price, $image, $description);

        if ($stmt->execute()) {
            $this->message = "Menu berhasil ditambahkan!";
            return true;
        } else {
            if (file_exists('images/menu/' . $image)) {
                unlink('images/menu/' . $image);
            }
            $this->error = "Gagal menambahkan menu: " . $stmt->error;
            return false;
        }
    }

    public function getMenuById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM menu WHERE id_menu = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateMenu($id_menu, $name, $type, $price, $description, $image) {
        $sql = "UPDATE menu SET 
                    name = ?, 
                    type = ?, 
                    price = ?, 
                    description = ?, 
                    image = ? 
                WHERE id_menu = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sisssi", $name, $type, $price, $description, $image, $id_menu);

        if ($stmt->execute()) {
            return "Menu berhasil diperbarui.";
        } else {
            return "Gagal memperbarui menu: " . $stmt->error;
        }
    }

    public function deleteMenu($id_menu, $current_image = '') {
        $stmt = $this->conn->prepare("DELETE FROM menu WHERE id_menu = ?");
        $stmt->bind_param("i", $id_menu);
        $result = $stmt->execute();
        $stmt->close();
        if ($result && !empty($current_image) && file_exists('images/menu/' . $current_image)) {
            unlink('images/menu/' . $current_image);
        }
        return $result;
    }
}
?>
