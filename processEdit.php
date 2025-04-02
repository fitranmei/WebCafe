<?php
include 'config.php';

// Tangkap data dari form
$id_menu = $_POST['id_menu'];
$name = $_POST['name'];
$type = $_POST['type'];
$price = $_POST['price'];
$description = $_POST['description'];
$current_image = $_POST['current_image'];

// Proses upload gambar
$image_name = $current_image; // Default gambar lama
if (!empty($_FILES['image']['name'])) {
    $target_dir = "images/";
    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    $max_file_size = 5 * 1024 * 1024; // 5MB

    // Validasi tipe dan ukuran file
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($file_extension, $allowed_types) && $_FILES['image']['size'] <= $max_file_size) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_name = $new_filename;
            
            // Hapus gambar lama jika ada dan berbeda
            if (!empty($current_image) && $current_image != $new_filename) {
                $old_file_path = $target_dir . $current_image;
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
            }
        } else {
            // Gagal upload
            $error = "Gagal mengunggah gambar.";
            header("Location: editMenu.php?idmenu=$id_menu&error=" . urlencode($error));
            exit();
        }
    } else {
        // Tipe atau ukuran file tidak valid
        $error = "File gambar tidak valid. Pastikan ukuran di bawah 5MB dan tipe file JPG, PNG, GIF, atau WebP.";
        header("Location: editMenu.php?idmenu=$id_menu&error=" . urlencode($error));
        exit();
    }
}

try {
    // Persiapkan query update
    $sql = "UPDATE menu SET 
            name = ?, 
            type = ?, 
            price = ?, 
            description = ?, 
            image = ? 
            WHERE id_menu = ?";
    
    // Eksekusi query
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisssi", $name, $type, $price, $description, $image_name, $id_menu);
    $result = $stmt->execute();

    if ($result) {
        // Redirect dengan pesan sukses
        $message = "Menu berhasil diperbarui.";
        header("Location: index.php?status=success&message=" . urlencode($message));
        exit();
    } else {
        // Gagal update
        $error = "Gagal memperbarui menu.";
        header("Location: editMenu.php?idmenu=$id_menu&error=" . urlencode($error));
        exit();
    }

} catch(Exception $e) {
    $error = "Gagal memperbarui menu: " . $e->getMessage();
    header("Location: editMenu.php?idmenu=$id_menu&error=" . urlencode($error));
    exit();
}
?>