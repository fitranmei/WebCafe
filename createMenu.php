<?php
require_once "models/Database.php";
require_once "models/Type.php";
require_once "models/Menu.php";

// Inisialisasi koneksi database dan model
$db = new Database();
$conn = $db->getConnection();
$typeManager = new Type();
$menu = new Menu();

$error = '';
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? '';
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';
    $image = $_FILES['image'] ?? null;

    if (!$menu->addMenu($name, $type, $price, $description, $image)) {
        $error = $menu->error;
    } else {
        $message = $menu->message;
    }
}

// Ambil daftar tipe menu untuk dropdown
$typesData = $typeManager->getAllTypes();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu Baru - Starbuck</title>
    <link rel="stylesheet" href="css/createMenu.css"> 
</head>
<body>
    <div class="container">
        <h1>Tambah Menu Baru</h1>
        <a href="index.php" class="back-link">← Kembali ke Daftar Menu</a>
        <div class="tema-icon">
            <i class="fa-solid fa-moon"></i> 
        </div>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($message): ?>
            <div class="success-message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Nama Menu:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="type">Tipe Menu:</label>
                <select id="type" name="type" required>
                    <option value="" disabled selected>-- Pilih Tipe Menu --</option>
                    <?php foreach ($typesData as $typeRow): ?>
                        <?php $sel = (isset($_POST['type']) && $_POST['type'] == $typeRow['id_type']) ? 'selected' : '' ?>
                        <option value="<?= $typeRow['id_type'] ?>" <?= $sel ?>>
                            <?= htmlspecialchars($typeRow['nama'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Harga (Rp):</label>
                <input type="number" id="price" name="price" min="1" required value="<?= htmlspecialchars($_POST['price'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="image">Gambar Menu:</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required>
                <small>Ukuran maks: 5MB. Tipe file: JPG, PNG, GIF, WebP</small>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description"><?= htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <button type="submit" class="submit-btn">Tambah Menu</button>
        </form>
    </div>

    <script src="js/script.js"></script>
    <script src="js/theme.js"></script> 
</body>
</html>
