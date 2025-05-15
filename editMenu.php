<?php
require_once "models/Database.php";
require_once "models/Menu.php";
require_once "models/Type.php";

$idMenu = $_GET['idmenu'] ?? null;
$error = $_GET['error'] ?? '';
$message = $_GET['message'] ?? '';

$menu = new Menu();
$typeManager = new Type();

$menuData = null;
if (!empty($idMenu)) {
    $menuData = $menu->getMenuById($idMenu);
    if (!$menuData) {
        $error = "Menu tidak ditemukan.";
    }
}

$types = $typeManager->getAllTypes();

// Proses jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_menu = $_POST['id_menu'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $current_image = $_POST['current_image'];

    $image_name = $current_image; 
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "images/menu/";
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        $max_file_size = 5 * 1024 * 1024; 

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
                $error = "Gagal mengunggah gambar.";
            }
        } else {
            $error = "File gambar tidak valid. Pastikan ukuran di bawah 5MB dan tipe file JPG, PNG, GIF, atau WebP.";
        }
    }

    if (empty($error)) {
        try {
            $result = $menu->updateMenu($id_menu, $name, $type, $price, $description, $image_name);
            if ($result === "Menu berhasil diperbarui.") {
                header("Location: index.php?status=success&message=" . urlencode($result));
                exit();
            } else {
                $error = $result;
            }
        } catch (Exception $e) {
            $error = "Gagal memperbarui menu: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu - Starbuck</title>
    <link rel="stylesheet" href="css/createMenu.css">
</head>
<body>
    <div class="container">
        <h1>Edit Menu</h1>
        <a href="index.php" class="back-link">← Kembali ke Daftar Menu</a>
        <div class="tema-icon">
            <i class="fa-solid fa-moon"></i> 
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="success-message">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
            <input type="hidden" name="id_menu" value="<?= htmlspecialchars($menuData['id_menu'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="name">Nama Menu:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($menuData['name'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="type">Tipe Menu:</label>
                <select id="type" name="type" required>
                <?php
                    foreach ($types as $type_row) {
                        $selected = ($menuData['type'] == $type_row['id_type']) ? 'selected' : '';
                        echo "<option value='{$type_row['id_type']}' $selected>{$type_row['nama']}</option>";
                    }
                ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Harga (Rp):</label>
                <input type="number" id="price" name="price" min="1" required value="<?= htmlspecialchars($menuData['price'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="image">Gambar Menu:</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                <small>Ukuran maks: 5MB. Tipe file: JPG, PNG, GIF, WebP</small>
                
                <?php if (!empty($menuData['image'])): ?>
                    <div class="current-image">
                        <p>Gambar Saat Ini:</p>
                        <img src="images/menu/<?= htmlspecialchars($menuData['image'], ENT_QUOTES, 'UTF-8') ?>" alt="Gambar Menu" style="max-width: 200px; margin-top: 10px;">
                        <input type="hidden" name="current_image" value="<?= htmlspecialchars($menuData['image'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description"><?= htmlspecialchars($menuData['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <button type="submit" class="submit-btn">Simpan Perubahan</button>
        </form>
    </div>

    <script src="js/script.js"></script>
    <script src="js/theme.js"></script>
</body>
</html>
