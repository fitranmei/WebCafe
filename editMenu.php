<?php 
include 'config.php';

$_idmenu = $_GET['idmenu'];
$error = isset($_GET['error']) ? $_GET['error'] : '';
$message = isset($_GET['message']) ? $_GET['message'] : '';

if(!empty($_idmenu)){
    $sql = "SELECT * FROM menu WHERE id_menu = ?";
    $st = $conn->prepare($sql);
    $st->bind_param("i", $_idmenu);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();
}

// Query untuk mendapatkan daftar tipe menu
$query_types = "SELECT * FROM types";
$result_types = mysqli_query($conn, $query_types);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu - Starling</title>
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
        <h1>Edit Menu</h1>
        <a href="index.php" class="back-link">← Kembali ke Daftar Menu</a>

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

        <form method="POST" action="processEdit.php" enctype="multipart/form-data">
            <input type="hidden" name="id_menu" value="<?= $row['id_menu'] ?>">

            <div class="form-group">
                <label for="name">Nama Menu:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="type">Tipe Menu:</label>
                <select id="type" name="type" required>
                    <?php
                    mysqli_data_seek($result_types, 0); 
                    while ($type_row = mysqli_fetch_assoc($result_types)) {
                        $selected = ($row['type'] == $type_row['id_type']) ? 'selected' : '';
                        echo "<option value='{$type_row['id_type']}' $selected>{$type_row['nama']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Harga (Rp):</label>
                <input type="number" id="price" name="price" min="1" required value="<?= htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="image">Gambar Menu:</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                <small>Ukuran maks: 5MB. Tipe file: JPG, PNG, GIF, WebP</small>
                
                <?php if (!empty($row['image'])): ?>
                    <div class="current-image">
                        <p>Gambar Saat Ini:</p>
                        <img src="images/<?= htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8') ?>" alt="Gambar Menu" style="max-width: 200px; margin-top: 10px;">
                        <input type="hidden" name="current_image" value="<?= htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description"><?= htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <button type="submit" class="submit-btn">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>