<?php
include 'includes/auth.php';
include 'includes/header.php';
include 'config/db.php';

if ($_SESSION['role'] != 'user') {
    echo "<p>Only users can book services.</p>";
    include 'includes/footer.php';
    exit();
}

$techid = $_GET['tech_id'];

$techq = mysqli_query($conn, "SELECT t.*, u.name
                               FROM technicians t
                               JOIN users u ON t.user_id = u.id
                               WHERE t.id = $techid");

if (mysqli_num_rows($techq) == 0) {
    echo "<p>Technician not found.</p>";
    include 'includes/footer.php';
    exit();
}

$tech = mysqli_fetch_assoc($techq);
?>

<div class="formcontainer" style="max-width:700px;">
    <h2>Book Service with <?php echo htmlspecialchars($tech['name']); ?></h2>
    <p><strong>Hourly Rate:</strong> ₹<?php echo number_format($tech['hourly_rate'], 2); ?>/hr</p>

    <form method="POST" action="book_process.php">
        <input type="hidden" name="techid" value="<?php echo $techid; ?>">
        <input type="hidden" name="rate" value="<?php echo $tech['hourly_rate']; ?>">

        <div class="formgroup">
            <label>Service Title</label>
            <input type="text" name="srvtitle" required>
        </div>

        <div class="formgroup">
            <label>Description</label>
            <textarea name="srvdesc"></textarea>
        </div>

        <div class="formgroup">
            <label>Scheduled Date</label>
            <input type="date" name="srvdate" required>
        </div>

        <div class="formgroup">
            <label>Scheduled Time</label>
            <input type="time" name="srvtime" required>
        </div>

        <div class="formgroup">
            <label>Duration (hours)</label>
            <select name="srvdur" required>
                <option value="1">1 hour</option>
                <option value="2">2 hours</option>
                <option value="3">3 hours</option>
                <option value="4">4 hours</option>
                <option value="5">5 hours</option>
                <option value="6">6 hours</option>
                <option value="8">8 hours</option>
            </select>
        </div>

        <h3 style="margin-top:20px;">Service Address</h3>

        <div class="formgroup">
            <label>Street Address</label>
            <input type="text" name="adrstreet" required>
        </div>

        <div class="formgroup">
            <label>City</label>
            <input type="text" name="adrcity" required>
        </div>

        <div class="formgroup">
            <label>State</label>
            <input type="text" name="adrstate" required>
        </div>

        <div class="formgroup">
            <label>Pincode</label>
            <input type="text" name="adrpin" required>
        </div>

        <div class="formgroup">
            <label>Contact Phone</label>
            <input type="text" name="cphone" required>
        </div>

        <button type="submit" class="btnprimary">Confirm Booking</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
