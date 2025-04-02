<?php
// Memasukkan file konfigurasi yang berisi pengaturan koneksi database
include 'config.php'; 

// Fungsi untuk mengambil semua jenis menu dari database
function getMenuTypes($conn) {
    // Query untuk mengambil nama jenis menu secara unik
    $query = "SELECT DISTINCT nama FROM types";
    $result = mysqli_query($conn, $query);
    $types = array();
    // Memasukkan setiap jenis menu ke dalam array
    while ($row = mysqli_fetch_assoc($result)) {
        $types[] = $row['nama'];
    }
    return $types;
}

// Rentang harga yang telah ditentukan sebelumnya
$priceRanges = [
    ['min' => 0, 'max' => 50000, 'label' => 'Rp 0 - Rp 50.000'],
    ['min' => 50000, 'max' => 100000, 'label' => 'Rp 50.000 - Rp 100.000'],
    ['min' => 100000, 'max' => 200000, 'label' => 'Rp 100.000 - Rp 200.000'],
    ['min' => 200000, 'max' => PHP_INT_MAX, 'label' => 'Lebih dari Rp 200.000']
];

// Mendapatkan daftar jenis menu yang tersedia
$types = getMenuTypes($conn);

// Jika tidak ada filter tipe, gunakan 'all'
$selectedType = isset($_GET['type']) ? $_GET['type'] : 'all';
// Jika tidak ada filter harga, kosongkan
$selectedPriceRange = isset($_GET['price']) ? $_GET['price'] : '';

// Fungsi untuk mengambil data menu dari database dengan filter
function getMenu($conn, $type = 'all', $priceRange = '') {
    // Query dasar untuk mengambil menu dengan join ke tabel types
    $query = "SELECT m.*, t.nama AS type_name FROM menu m JOIN types t ON m.type = t.id_type";
    
    // Tambahkan filter berdasarkan tipe menu
    if ($type !== 'all') {
        $query .= " AND t.nama = '" . mysqli_real_escape_string($conn, $type) . "'";
    }
    
    // Tambahkan filter berdasarkan rentang harga
    if (!empty($priceRange)) {
        $range = explode('-', $priceRange);
        if (count($range) === 2) {
            $minPrice = (int)$range[0];
            $maxPrice = (int)$range[1];
            $query .= " AND m.price BETWEEN $minPrice AND $maxPrice";
        }
    }
    
    // Eksekusi query dan simpan hasilnya dalam array
    $result = mysqli_query($conn, $query);
    $menu = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $menu[] = $row;
    }
    return $menu;
}

// Ambil data menu sesuai filter yang dipilih
$menu = getMenu($conn, $selectedType, $selectedPriceRange);

// Fungsi untuk menghapus menu dari database
function deleteProduct($conn, $id_menu, $current_image) {
    $stmt = mysqli_prepare($conn, "DELETE FROM menu WHERE id_menu = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_menu);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Hapus file gambar terkait
    $target_dir = 'images/';
    $old_file_path = $target_dir . $current_image;
    if (file_exists($old_file_path)) {
        unlink($old_file_path);
    }
    return $result;
}

// Proses penghapusan menu jika ada permintaan POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete']) && $_POST['delete']) {
    $result = deleteProduct($conn, $_POST['delete'], $_POST['image']);
    // Redirect dengan status operasi
    header("Location: index.php?status=" . ($result ? "deleted" : "error"));
    exit();
}

// Fungsi untuk memformat harga dalam rupiah
function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

// Fungsi untuk membersihkan output agar aman dari serangan XSS
function sanitizeOutput($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Starling</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/6ed949fe3b.js" crossorigin="anonymous"></script>
    <!-- <style>
        * {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;
        }

        body {
            background-color:rgb(164, 204, 163);
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 25px;
            background-color:rgb(248, 248, 248);
            border-radius: 15px;
        }

        h1 {
            text-align: center;
            color: #28a745;
            margin: 40px 0;
            font-size: 32px;
            font-weight: bolder;
        }

        .sell-link {
            text-decoration: none;
            color: white;
            padding: 8px 16px;
            background-color: #28a745;
            border-radius: 10px;
            font-weight: 600;
            width: 18px;
            height: 18px;
        }

        .sell-link:hover {
            background-color: #1D741B;
            transform: scale(1.05);
        }

        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1.2fr));
            gap: 20px;
            margin-top: 20px;
        }

        .product {
            background: white;
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            display: flex;
            flex-direction: row;
            margin: 1px;
            gap: 20px;
            position: relative;
            border: 1px white solid;
        }

        .product:hover {
            border: 1px solid #1D741B;
        }

        .product-image {
            background: white;
            width: 120px;
            height: 120px;
            border-radius: 8px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 14px;
            position: relative;
            object-fit: contain;
        }

        .product-detail {
            display: flex;
            flex-direction: column;
            align-items: start;
            margin: 0;
            width: 100%; 
            position: relative; 
            min-height: 120px; 
        }

        .product h2 {
            color: black;
            font-size: 16px;
            margin: 0;
            padding: 0;
            height: 46px;
        }

        .product p {
            color: gray;
            font-size: 14px;
            margin: 0;
        }

        .product h3 {
            color: black;
            font-size: 16px;
            margin: 2px;
        }

        .buy-link {
            text-decoration: none;
            color: white;
            background-color: #1D741B;
            border-radius: 100%;
            border: none !important;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            cursor: pointer;
            position: absolute; 
            bottom: 0; 
            right: 0; 
            font-weight: bolder;
        }

        .delete-menu {
            text-decoration: none;
            color: white;
            font-size: 12px;
            padding: 6px;
            background-color: #c22323;
            border-radius: 6px;
            display: flex; 
            align-items: center; 
            justify-content: center;
        }

        .menu-filter {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            flex-direction: column;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-dropdown {
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
            background-color: white;
            font-size: 14px;
            min-width: 180px;
        }

        .filter-dropdown:hover {
            border-color: #aaa;
        }

        label {
            font-weight: 500;
        }

        .status-message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .product-action {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 3px;
        }
        .delete-form {
            display: inline;
        }
        .delete-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
        .edit-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .edit-button:hover {
            background-color: #218838;
        }
    </style> -->
</head>
<body>
    <div class="container">
        <h1>Daftar Menu</h1>
        <!-- Tombol untuk membuat menu baru -->
        <a href="createMenu.php" class="sell-link">Buat</a>

        <!-- Menampilkan pesan status operasi -->
        <?php if (isset($_GET['status']) || isset($_GET['message'])): ?>
            <div class="status-message <?= 
                (isset($_GET['status']) && $_GET['status'] === 'deleted' ? 'success' : 
                (isset($_GET['status']) && $_GET['status'] === 'error' ? 'error' : 
                (isset($_GET['status']) && $_GET['status'] === 'success' ? 'success' : ''))) 
            ?>">
                <?php if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
                    Menu berhasil dihapus.
                <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
                    Terjadi kesalahan saat menghapus menu.
                <?php elseif (isset($_GET['message'])): ?>
                    <?= sanitizeOutput($_GET['message']) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Filter menu -->
        <h3>Filter by:</h3>
        <div class="menu-filter">
            <form method="GET" action="" id="filterForm" class="menu-filter">
                <div class="filter-group">
                    <!-- Dropdown filter tipe menu -->
                    <select id="type-filter" name="type" class="filter-dropdown" onchange="document.getElementById('filterForm').submit();">
                        <option value="all" <?= $selectedType === 'all' ? 'selected' : '' ?>>Semua Tipe</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= sanitizeOutput($type) ?>" <?= $selectedType === $type ? 'selected' : '' ?>>
                                <?= sanitizeOutput($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Dropdown filter rentang harga -->
                    <select id="price-filter" name="price" class="filter-dropdown" onchange="document.getElementById('filterForm').submit();">
                        <option value="" <?= empty($selectedPriceRange) ? 'selected' : '' ?>>Semua Harga</option>
                        <?php foreach ($priceRanges as $range): ?>
                            <option value="<?= $range['min'] . '-' . $range['max'] ?>" 
                                    <?= $selectedPriceRange === ($range['min'] . '-' . $range['max']) ? 'selected' : '' ?>>
                                <?= sanitizeOutput($range['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>

        <!-- Container untuk menampilkan daftar menu -->
        <div class="product-container">
            <?php if (empty($menu)): ?>
                <p>Tidak ada menu yang tersedia</p>
            <?php else: ?>
                <?php foreach ($menu as $item): ?>
                    <div class="product">
                        <img class="product-image" src="images/<?= sanitizeOutput($item['image']); ?>" alt="<?= sanitizeOutput($item['name']); ?>">
                        <div class="product-detail">
                            <h2><?= sanitizeOutput($item['name']); ?></h2>
                            <p><?= sanitizeOutput($item['type_name']); ?></p>
                            <h3><?= formatPrice($item['price']); ?></h3>

                            <div class="product-action">
                                <form action="editMenu.php" method="GET">
                                    <input type="hidden" name="idmenu" value="<?= htmlspecialchars($item['id_menu'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" class="edit-button">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </form>
                                <form method="POST" action="index.php" class="delete-form" onsubmit="return confirm('Apakah Anda yakin ingin menghapus <?= sanitizeOutput($item['name']); ?>?');">
                                    <input type="hidden" name="delete" value="<?= sanitizeOutput($item['id_menu']); ?>">
                                    <input type="hidden" name="image" value="<?= sanitizeOutput($item['image']); ?>">
                                    <button type="submit" class="delete-button">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>