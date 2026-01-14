<?php
session_start();
include 'config.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password'];

$query = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE username='$username' LIMIT 1"
);

if (mysqli_num_rows($query) === 1) {
    $user = mysqli_fetch_assoc($query);

    if (password_verify($password, $user['password'])) {

        $_SESSION['user'] = [
            'id'       => $user['id_user'],
            'username' => $user['username'],
            'role'     => $user['role']
        ];

        header("Location: ../Template/dashboard.php");
        exit;
    }
}

header("Location: ../Template/login.php?error=1");
exit;
