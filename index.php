<?php
require_once "models/Database.php";
require_once "models/Type.php";
require_once "models/Menu.php";

$db = new Database();
$conn = $db->getConnection();

session_start();

$isLoggedIn = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
$userName = $_SESSION['user_name'] ?? '';

$typeManager = new Type();
$menuManager = new Menu();

$typesData = $typeManager->getAllTypes();
$types = array_column($typesData, 'nama');

$selectedType = $_GET['type'] ?? 'all';

function getMenu($conn, $type = 'all') {
    $query = "SELECT m.*, t.nama AS type_name FROM menu m JOIN types t ON m.type = t.id_type WHERE 1=1";
    if ($type !== 'all') {
        $query .= " AND t.nama = '" . mysqli_real_escape_string($conn, $type) . "'";
    }

    $result = $conn->query($query);
    $menu = []; 
    while ($row = $result->fetch_assoc()) {
        $menu[] = $row;
    }

    return $menu;
}

$menu = getMenu($conn, $selectedType);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $result = $menuManager->deleteMenu($_POST['delete'], $_POST['image'] ?? '');
    header("Location: index.php?status=" . ($result ? "deleted" : "error"));
    exit;
}

function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

function sanitizeOutput($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Starbuck</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/6ed949fe3b.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <section class="header">
            <div class="logo">
                <img src="images/general/Starbucks_Corporation_Logo_2011.svg" alt="Starbucks_Corporation_Logo_2011.svg">
            </div>
            <div class="search-container">
                <div class="search-bar">
                    <div class="search-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" id="menu-search" class="search-input" placeholder="Cari menu...">
                </div>
            </div>
            <div class="user-actions">
                <div class="tema-icon">
                    <i class="fa-solid fa-sun"></i>
                </div>
                <?php if ($isLoggedIn): ?>
                    <a href="auth/logout.php" class="login-btn">Logout</a>
                <?php else: ?>
                    <a href="auth/login.php" class="login-btn">Masuk/Daftar</a>
                <?php endif; ?>
            </div>
        </section>

        <section class="menu-actions">
            <div class="menu-tabs" id="type-filter">
                <a class="menu-tab <?= $selectedType === 'all' ? 'active' : '' ?>" href="?type=all<?= isset($selectedPriceRange) ? '&price=' . urlencode($selectedPriceRange) : '' ?>">All</a>

                <?php foreach ($types as $type): ?>
                    <a class="menu-tab <?= $selectedType === $type ? 'active' : '' ?>" href="?type=<?= urlencode($type) ?><?= isset($selectedPriceRange) ? '&price=' . urlencode($selectedPriceRange) : '' ?>" data-type="<?= sanitizeOutput($type) ?>">
                        <?= sanitizeOutput($type) ?>
                    </a>
                <?php endforeach; ?>
            </div>


            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a class="menu-add" href="createMenu.php">Tambah Menu</a>
            <?php endif; ?>
        </section>
    </header>

    <section class="carousel-container">
        <div class="carousel">
            <div class="carousel-item active">
                <img src="images/general/banner promo starling.jpg" alt="Starbucks Promo 1">
            </div>
            <div class="carousel-item">
                <img src="images/general/banner promo starling.jpg" alt="Starbucks Promo 2">
            </div>
            <div class="carousel-item">
                <img src="images/general/banner promo starling.jpg" alt="Starbucks Promo 2">
            </div>
        </div>
        
        <button class="carousel-prev">
            <i class="fa-solid fa-angle-left"></i>
        </button>
        
        <button class="carousel-next">
            <i class="fa-solid fa-angle-right"></i>
        </button>
        
        <div class="carousel-indicators">
            <span class="indicator active" data-index="0"></span>
            <span class="indicator" data-index="1"></span>
            <span class="indicator" data-index="2"></span>
        </div>
    </section>

    <section class="menu-section">
        <h2 class="section-title">Menu</h2>

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

        <div class="menu-items" id="menu-items">
            <?php if (empty($menu)): ?>
                <p>Tidak ada menu yang tersedia</p>
            <?php else: ?>
                <?php foreach ($menu as $item): ?>
                    <div class="menu-item" data-name="<?= strtolower($item['name']); ?>">
                        <img src="images/menu/<?= sanitizeOutput($item['image']); ?>" alt="<?= sanitizeOutput($item['name']); ?>" class="item-image">
                        <div class="product-detail">
                            <div class="item-title"><?= sanitizeOutput($item['name']); ?></div>
                            <div class="item-description"><?= sanitizeOutput($item['type_name']); ?></div>
                            <div class="item-price"><?= formatPrice($item['price']); ?></div>

                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <div class="product-action">
                                    <form action="editMenu.php" method="GET">
                                        <input type="hidden" name="idmenu" value="<?= sanitizeOutput($item['id_menu']); ?>">
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
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <script src="js/script.js"></script>
    <script src="js/theme.js"></script>
    <script src="js/jQuery.js"></script>
</body>
</html>
