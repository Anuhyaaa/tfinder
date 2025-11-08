<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
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
                <a href="index.php">Home</a>
                <a href="search.php">Find Technicians</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <?php if ($_SESSION['role'] == 'user'): ?>
                        <a href="my_bookings.php">My Bookings</a>
                    <?php else: ?>
                        <a href="jobs.php">My Jobs</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['name']); ?>)</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="mainwrap">
