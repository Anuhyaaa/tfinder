<?php
include 'includes/auth.php';
include 'includes/header.php';
include 'config/db.php';

if ($_SESSION['role'] != 'user') {
    echo "<p>Only users can leave reviews.</p>";
    include 'includes/footer.php';
    exit();
}

$techid = $_GET['tech_id'];
$bookid = $_GET['booking_id'];

$techq = mysqli_query($conn, "SELECT u.name
                               FROM technicians t
                               JOIN users u ON t.user_id = u.id
                               WHERE t.id = $techid");

if (mysqli_num_rows($techq) == 0) {
    echo "<p>Technician not found.</p>";
    include 'includes/footer.php';
    exit();
}

$tech = mysqli_fetch_assoc($techq);

$checkrev = mysqli_query($conn, "SELECT * FROM reviews WHERE user_id = {$_SESSION['user_id']} AND technician_id = $techid");
$existingreview = mysqli_fetch_assoc($checkrev);
$isedit = mysqli_num_rows($checkrev) > 0;
?>

<div class="formcontainer">
    <h2><?php echo $isedit ? 'Edit Review' : 'Leave Review'; ?> for <?php echo htmlspecialchars($tech['name']); ?></h2>

    <?php if ($isedit): ?>
        <div class="alertbox alertsuccess" style="margin-bottom:20px;">
            You have already reviewed this technician. You can update your review below.
        </div>
    <?php endif; ?>

    <form method="POST" action="review_process.php">
        <input type="hidden" name="techid" value="<?php echo $techid; ?>">
        <input type="hidden" name="bookid" value="<?php echo $bookid; ?>">
        <input type="hidden" name="isedit" value="<?php echo $isedit ? '1' : '0'; ?>">

        <div class="formgroup">
            <label>Rating (1-5)</label>
            <select name="rating" required>
                <option value="">Select Rating</option>
                <option value="5" <?php echo ($isedit && $existingreview['rating'] == 5) ? 'selected' : ''; ?>>5 - Excellent</option>
                <option value="4" <?php echo ($isedit && $existingreview['rating'] == 4) ? 'selected' : ''; ?>>4 - Good</option>
                <option value="3" <?php echo ($isedit && $existingreview['rating'] == 3) ? 'selected' : ''; ?>>3 - Average</option>
                <option value="2" <?php echo ($isedit && $existingreview['rating'] == 2) ? 'selected' : ''; ?>>2 - Poor</option>
                <option value="1" <?php echo ($isedit && $existingreview['rating'] == 1) ? 'selected' : ''; ?>>1 - Very Poor</option>
            </select>
        </div>

        <div class="formgroup">
            <label>Comment</label>
            <textarea name="comment" rows="5"><?php echo $isedit ? htmlspecialchars($existingreview['comment']) : ''; ?></textarea>
        </div>

        <button type="submit" class="btnprimary"><?php echo $isedit ? 'Update Review' : 'Submit Review'; ?></button>
        <a href="my_bookings.php" class="btnwarning" style="margin-left:10px;">Cancel</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
