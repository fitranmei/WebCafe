<?php
// config.php
$host     = 'localhost';
$username = 'root';
$password = ''; 
$database = 'malassssss';

// Membuat koneksi ke MySQL
$conn = mysqli_connect($host, $username, $password, $database);

// Jika koneksi gagal, tampilkan pesan error
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
