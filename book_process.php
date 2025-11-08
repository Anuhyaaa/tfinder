<?php
include 'includes/auth.php';
include 'config/db.php';

$techid = $_POST['techid'];
$rate = $_POST['rate'];
$srvtitle = $_POST['srvtitle'];
$srvdesc = $_POST['srvdesc'];
$srvdate = $_POST['srvdate'];
$srvtime = $_POST['srvtime'];
$srvdur = $_POST['srvdur'];
$adrstreet = $_POST['adrstreet'];
$adrcity = $_POST['adrcity'];
$adrstate = $_POST['adrstate'];
$adrpin = $_POST['adrpin'];
$cphone = $_POST['cphone'];

$totalamt = $rate * $srvdur;
$uid = $_SESSION['user_id'];

$insertsql = "INSERT INTO bookings (user_id, technician_id, service, description, scheduled_date, scheduled_time, duration, total_amount, address_street, address_city, address_state, address_pincode, contact_phone, status)
              VALUES ($uid, $techid, '$srvtitle', '$srvdesc', '$srvdate', '$srvtime', $srvdur, $totalamt, '$adrstreet', '$adrcity', '$adrstate', '$adrpin', '$cphone', 'pending')";

mysqli_query($conn, $insertsql);

header("Location: my_bookings.php");
exit();
?>
