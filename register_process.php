<?php
session_start();
include 'config/db.php';

$uname = $_POST['uname'];
$uemail = $_POST['uemail'];
$upass = $_POST['upass'];
$roletype = $_POST['roletype'];

$checkmail = mysqli_query($conn, "SELECT id FROM users WHERE email = '$uemail'");
if (mysqli_num_rows($checkmail) > 0) {
    header("Location: register.php?error=email_exists");
    exit();
}

$hashpass = password_hash($upass, PASSWORD_DEFAULT);

$insertsql = "INSERT INTO users (name, email, password, role) VALUES ('$uname', '$uemail', '$hashpass', '$roletype')";
mysqli_query($conn, $insertsql);
$newuid = mysqli_insert_id($conn);

if ($roletype == 'technician') {
    $catid = $_POST['catid'];
    $tcity = $_POST['tcity'];
    $tstate = $_POST['tstate'];
    $tphone = $_POST['tphone'];
    $tbio = $_POST['tbio'];
    $tskills = $_POST['tskills'];
    $texp = $_POST['texp'];
    $trate = $_POST['trate'];

    $techsql = "INSERT INTO technicians (user_id, category_id, city, state, phone, bio, skills, experience, hourly_rate)
                VALUES ($newuid, $catid, '$tcity', '$tstate', '$tphone', '$tbio', '$tskills', $texp, $trate)";
    mysqli_query($conn, $techsql);
}

$_SESSION['user_id'] = $newuid;
$_SESSION['name'] = $uname;
$_SESSION['role'] = $roletype;

header("Location: dashboard.php");
exit();
?>
