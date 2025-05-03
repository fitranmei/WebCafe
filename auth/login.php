<?php
session_start();
require_once "../User.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = new User();
    $loggedUser = $user->loginUser($email, $password);

    if ($loggedUser) {
        $_SESSION['is_logged_in'] = true;
        $_SESSION['user_name'] = $loggedUser['name']; 
        $_SESSION['user_id'] = $loggedUser['id']; 

        header("Location: ../index.php"); 
        exit;
    } else {
        $errors[] = "Email atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login Akun</title>
    <link rel="stylesheet" href="../css/register.css">
    <script src="https://kit.fontawesome.com/6ed949fe3b.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="left-section">
        </div>

        <div class="right-section">
            <div class="card">
                <h2 class="title">Login Account</h2>
                <p class="subtitle">Belum punya akun? <a href="register.php">Register di sini</a></p>

                <?php if (!empty($errors)): ?>
                    <div class="error-box">
                        <ul>
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form class="form" method="POST">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group password-field">
                        <label>Password</label>
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle" onclick="togglePassword('password')">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    <button type="submit" class="submit-button" id="submitBtn">Login</button>
                </form>
                <div>
                    <p>email: admin@gmail.com</p>
                    <p>pass: 123456</p>
                </div>
            </div>
        </div>

    </div>

    <script src="../script.js"></script>
</body>
</html>
