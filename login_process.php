<?php
session_start();
include 'config/db.php';

$uemail = $_POST['uemail'];
$upass = $_POST['upass'];

$result = mysqli_query($conn, "SELECT id, password, role, name FROM users WHERE email = '$uemail'");

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    if (password_verify($upass, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['role'] = $row['role'];

        header("Location: dashboard.php");
        exit();
    }
}

header("Location: login.php?error=1");
exit();
?>
