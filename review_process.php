<?php
include 'includes/auth.php';
include 'config/db.php';

$techid = $_POST['techid'];
$rating = $_POST['rating'];
$comment = $_POST['comment'];
$uid = $_SESSION['user_id'];

$insertsql = "INSERT INTO reviews (user_id, technician_id, rating, comment)
              VALUES ($uid, $techid, $rating, '$comment')";

mysqli_query($conn, $insertsql);

$updatesql = "UPDATE technicians
              SET average_rating = (SELECT AVG(rating) FROM reviews WHERE technician_id = $techid)
              WHERE id = $techid";

mysqli_query($conn, $updatesql);

header("Location: my_bookings.php");
exit();
?>
