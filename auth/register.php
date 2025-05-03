<?php
require_once "../User.php";

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = $_POST['fullName'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $role = 'user';
    $profilePicture = 'user.jpg';

    if ($password !== $confirmPassword) {
        $errors[] = "Password dan konfirmasi tidak cocok.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }

    $user = new User();
    if ($user->isEmailOrUsernameExists($email, $username)) {
        $errors[] = "Email atau username sudah digunakan.";
    }

    if (empty($errors)) {
        if ($user->createUser($fullName, $username, $email, $password, $role, $profilePicture)) {
            $success = true;
        } else {
            $errors[] = "Gagal menyimpan data ke database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Register Akun</title>
    <link rel="stylesheet" href="../css/register.css">
    <script src="https://kit.fontawesome.com/6ed949fe3b.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="left-section">
        </div>

        <div class="right-section">
            <div class="card">
                <h2 class="title">Create Account</h2>
                <p class="subtitle">
                    Have an account? <a href="login.php">Login here</a>
                </p>

                <?php if (!empty($errors)): ?>
                    <div class="error-box">
                        <ul>
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php elseif ($success): ?>
                    <div class="success-box">
                        Akun berhasil dibuat. <a href="login.php">Login sekarang</a>.
                    </div>
                <?php endif; ?>

                <form class="form" method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullName" required>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="form-group password-field">
                        <label>Password</label>
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle" onclick="togglePassword('password')"><i class="fa-solid fa-eye"></i></button>
                    </div>

                    <div class="form-group password-field">
                        <label>Password Confirmation</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required>
                        <button type="button" class="toggle" onclick="togglePassword('confirmPassword')"><i class="fa-solid fa-eye"></i></button>
                    </div>

                    <button type="submit" class="submit-button">Register</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../script.js"></script>
</body>
</html>