<?php
session_start();

$response = [
    'loggedIn' => isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true,
    'userName' => $_SESSION['user_name'] ?? null,
    'userId' => $_SESSION['user_id'] ?? null
];

header('Content-Type: application/json');
echo json_encode($response);
?>