<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
include 'includes/header.php';
?>

<div class="formcontainer">
    <h2>Login</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="alertbox alerterror">Invalid email or password</div>
    <?php endif; ?>

    <form method="POST" action="login_process.php">
        <div class="formgroup">
            <label>Email</label>
            <input type="email" name="uemail" required>
        </div>

        <div class="formgroup">
            <label>Password</label>
            <input type="password" name="upass" required>
        </div>

        <button type="submit" class="btnprimary">Login</button>
    </form>

    <p style="margin-top:20px;">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include 'includes/footer.php'; ?>
