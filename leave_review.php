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

$checkrev = mysqli_query($conn, "SELECT id FROM reviews WHERE user_id = {$_SESSION['user_id']} AND technician_id = $techid");
if (mysqli_num_rows($checkrev) > 0) {
    echo "<div class='alertbox alerterror'>You have already reviewed this technician.</div>";
    echo "<a href='my_bookings.php' class='btnprimary'>Back to Bookings</a>";
    include 'includes/footer.php';
    exit();
}
?>

<div class="formcontainer">
    <h2>Leave Review for <?php echo htmlspecialchars($tech['name']); ?></h2>

    <form method="POST" action="review_process.php">
        <input type="hidden" name="techid" value="<?php echo $techid; ?>">
        <input type="hidden" name="bookid" value="<?php echo $bookid; ?>">

        <div class="formgroup">
            <label>Rating (1-5)</label>
            <select name="rating" required>
                <option value="">Select Rating</option>
                <option value="5">5 - Excellent</option>
                <option value="4">4 - Good</option>
                <option value="3">3 - Average</option>
                <option value="2">2 - Poor</option>
                <option value="1">1 - Very Poor</option>
            </select>
        </div>

        <div class="formgroup">
            <label>Comment</label>
            <textarea name="comment" rows="5"></textarea>
        </div>

        <button type="submit" class="btnprimary">Submit Review</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
