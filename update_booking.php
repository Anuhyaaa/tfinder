<?php
include 'includes/auth.php';
include 'config/db.php';

$bookid = $_GET['id'];
$newstatus = $_GET['status'];

$updatesql = "UPDATE bookings SET status = '$newstatus' WHERE id = $bookid";
mysqli_query($conn, $updatesql);

$returnpage = isset($_GET['return']) ? $_GET['return'] : '';

if ($_SESSION['role'] == 'user') {
    header("Location: my_bookings.php");
} else {
    if ($returnpage == 'jobs') {
        header("Location: jobs.php");
    } else {
        header("Location: jobs.php");
    }
}
exit();
?>
