<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$currentpage = basename($_SERVER['PHP_SELF']);
$searchpages = ['search.php', 'technician.php', 'book.php'];
$dashpages = ['dashboard.php', 'update_booking.php', 'leave_review.php'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>T-Finder - Find Local Technicians</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="topnav">
        <div class="navcontainer">
            <a href="index.php" class="logo">T-Finder</a>
            <div class="navlinks">
                <a href="index.php" class="<?php echo ($currentpage == 'index.php') ? 'active' : ''; ?>">Home</a>
                <?php if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'user'): ?>
                    <a href="search.php" class="<?php echo (in_array($currentpage, $searchpages)) ? 'active' : ''; ?>">Find Technicians</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="<?php echo (in_array($currentpage, $dashpages)) ? 'active' : ''; ?>">Dashboard</a>
                    <?php if ($_SESSION['role'] == 'user'): ?>
                        <a href="my_bookings.php" class="<?php echo ($currentpage == 'my_bookings.php') ? 'active' : ''; ?>">My Bookings</a>
                    <?php else: ?>
                        <a href="jobs.php" class="<?php echo ($currentpage == 'jobs.php') ? 'active' : ''; ?>">My Jobs</a>
                    <?php endif; ?>
                    <a href="logout.php" class="<?php echo ($currentpage == 'logout.php') ? 'active' : ''; ?>">Logout (<?php echo htmlspecialchars($_SESSION['name']); ?>)</a>
                <?php else: ?>
                    <a href="login.php" class="<?php echo ($currentpage == 'login.php') ? 'active' : ''; ?>">Login</a>
                    <a href="register.php" class="<?php echo ($currentpage == 'register.php') ? 'active' : ''; ?>">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="mainwrap">
