<?php
include 'config.php'; 

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $price = (int)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    //Pengecekan Duplikasi Data: Periksa apakah nama produk sama
    $stmt = $conn->prepare("SELECT id_menu FROM menu WHERE name = ?");
    if ($stmt === false) {
        $error = "Terjadi kesalahan: " . $conn->error;
    } else {
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            $error = "Gagal mengeksekusi query: " . $stmt->error;
        } else {
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = "Pendaftaran gagal: Nama Menu telah ada";
            }
        }
        $stmt->close();
    }

    // Validasi dan upload gambar
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_file_size = 5 * 1024 * 1024; // 5MB

        // Cek tipe file
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $error = "Tipe file tidak diizinkan. Hanya JPG, PNG, GIF, dan WebP yang diperbolehkan.";
        }
        // Cek ukuran file
        elseif ($_FILES['image']['size'] > $max_file_size) {
            $error = "Ukuran file terlalu besar. Maksimal 5MB.";
        }
        else {
            // Buat folder images jika belum ada
            if (!file_exists('images')) {
                mkdir('images', 0777, true);
            }

            // Generate nama file unik
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid('menu_') . '.' . $file_extension;
            $upload_path = 'images/' . $image_name;

            // Upload file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $image_name;
            } else {
                $error = "Gagal mengupload gambar.";
            }
        }
    } else {
        $error = "Silakan pilih gambar";
    }

    // Validasi input
    if (empty($name)) {
        $error = "Nama menu tidak boleh kosong";
    } elseif (empty($type)) {
        $error = "Tipe menu tidak boleh kosong";
    } elseif ($price <= 0) {
        $error = "Harga harus lebih dari 0";
    } elseif (empty($image)) {
        $error = "Gagal upload gambar";
    } else {
        // Gunakan prepared statement untuk mencegah SQL Injection
        $stmt = $conn->prepare("INSERT INTO menu (name, type, price, image, description) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            $error = "Gagal menyiapkan query: " . $conn->error;
        } else {
            $stmt->bind_param("sisss", $name, $type, $price, $image, $description);
            
            if ($stmt->execute()) {
                $message = "Menu berhasil ditambahkan!";
                $_POST = [];
            } else {
                // Hapus file yang sudah diupload jika query gagal
                if (file_exists('images/' . $image)) {
                    unlink('images/' . $image);
                }
                $error = "Gagal menambahkan menu: " . $stmt->error;
            }
            
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu Baru - Starling</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="createMenu.css">
    <style>
        body {
            background-color:rgb(164, 204, 163);
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background-color:rgb(248, 248, 248);
            border-radius: 15px;
        }

        .form-container {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            margin-bottom: 6px;
            resize: vertical;
            overflow: hidden !important;
        }

        .submit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-btn:hover {
            background-color: #218838;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .custom-type-container {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah Menu Baru</h1>
        <a href="index.php" class="back-link">← Kembali ke Daftar Menu</a>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="success-message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Nama Menu:</label>
                <input type="text" id="name" name="name" required value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : '' ?>">
            </div>

            <div class="form-group">
                <label for="type">Tipe Menu:</label>
                <select id="type" name="type" required>
                    <option value="" disabled="disabled" selected="selected">Pilih Tipe Menu</option>
                    <?php
                    $query = "SELECT * FROM types";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $selected = (isset($_POST['type']) && $_POST['type'] == $row['id_type']) ? 'selected' : '';
                        echo "<option value='{$row['id_type']}' $selected>{$row['nama']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Harga (Rp):</label>
                <input type="number" id="price" name="price" min="1" required value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price'], ENT_QUOTES, 'UTF-8') : '' ?>">
            </div>

            <div class="form-group">
                <label for="image">Gambar Menu:</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required>
                <small>Ukuran maks: 5MB. Tipe file: JPG, PNG, GIF, WebP</small>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8') : '' ?></textarea>
            </div>

            <button type="submit" class="submit-btn">Tambah Menu</button>
        </form>
    </div>
</body>
</html>