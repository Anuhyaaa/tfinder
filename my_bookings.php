<?php
include 'includes/auth.php';
include 'includes/header.php';
include 'config/db.php';

if ($_SESSION['role'] != 'user') {
    echo "<p>Access denied.</p>";
    include 'includes/footer.php';
    exit();
}

$uid = $_SESSION['user_id'];

$bookings = mysqli_query($conn, "SELECT b.*, u.name as tech_name, r.id as review_id, r.rating, r.comment
                                  FROM bookings b
                                  JOIN technicians t ON b.technician_id = t.id
                                  JOIN users u ON t.user_id = u.id
                                  LEFT JOIN reviews r ON r.user_id = $uid AND r.technician_id = b.technician_id
                                  WHERE b.user_id = $uid
                                  ORDER BY b.scheduled_date DESC");
?>

<h2>My Bookings</h2>

<div class="bookingscontainer">
    <?php if (mysqli_num_rows($bookings) > 0): ?>
        <?php while($book = mysqli_fetch_assoc($bookings)): ?>
            <div class="bookingcard">
                <div class="bookingheader">
                    <div>
                        <h3><?php echo htmlspecialchars($book['tech_name']); ?></h3>
                        <p style="color:#6b7280;margin:5px 0;"><?php echo htmlspecialchars($book['service']); ?></p>
                    </div>
                    <span class="statusbadge status<?php echo $book['status']; ?>">
                        <?php echo ucfirst($book['status']); ?>
                    </span>
                </div>

                <div class="bookingdetails">
                    <div class="bookinginfo">
                        <div class="infoitem">
                            <strong>Date & Time</strong>
                            <p><?php echo date('M d, Y', strtotime($book['scheduled_date'])); ?> at <?php echo date('h:i A', strtotime($book['scheduled_time'])); ?></p>
                        </div>
                        <div class="infoitem">
                            <strong>Duration</strong>
                            <p><?php echo $book['duration']; ?> hours</p>
                        </div>
                        <div class="infoitem">
                            <strong>Total Amount</strong>
                            <p class="priceinfo">₹<?php echo number_format($book['total_amount'], 2); ?></p>
                        </div>
                    </div>

                    <?php if ($book['status'] == 'completed' && $book['review_id']): ?>
                        <div class="reviewbox">
                            <strong>Your Review</strong>
                            <div style="margin-top:8px;">
                                <span class="ratingstar">★</span>
                                <span style="font-weight:600;color:#1f2937;"><?php echo $book['rating']; ?>/5</span>
                            </div>
                            <?php if($book['comment']): ?>
                                <p style="margin-top:10px;color:#6b7280;line-height:1.6;"><?php echo nl2br(htmlspecialchars($book['comment'])); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="bookingactions">
                        <?php if ($book['status'] == 'pending' || $book['status'] == 'confirmed'): ?>
                            <a href="update_booking.php?id=<?php echo $book['id']; ?>&status=cancelled" class="btndanger" onclick="return confirm('Cancel this booking?')">Cancel</a>
                        <?php endif; ?>

                        <?php if ($book['status'] == 'completed'): ?>
                            <?php if ($book['review_id']): ?>
                                <a href="leave_review.php?booking_id=<?php echo $book['id']; ?>&tech_id=<?php echo $book['technician_id']; ?>" class="btnprimary">Edit Review</a>
                            <?php else: ?>
                                <a href="leave_review.php?booking_id=<?php echo $book['id']; ?>&tech_id=<?php echo $book['technician_id']; ?>" class="btnsuccess">Leave Review</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;padding:40px;color:#7f8c8d;">No bookings yet.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
