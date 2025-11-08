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

<div class="pagetitle">
    <h2>📅 My Bookings</h2>
    <p>Manage and track all your service bookings</p>
</div>

<div class="bookingscontainer">
    <?php if (mysqli_num_rows($bookings) > 0): ?>
        <?php while($book = mysqli_fetch_assoc($bookings)): ?>
            <div class="bookingcard enhanced">
                <div class="bookingheader" onclick="toggleBooking(this)">
                    <div class="bookingheaderleft">
                        <div class="technicianicon">👨‍🔧</div>
                        <div>
                            <h3><?php echo htmlspecialchars($book['tech_name']); ?></h3>
                            <p class="servicetype">🔧 <?php echo htmlspecialchars($book['service']); ?></p>
                        </div>
                    </div>
                    <div class="bookingheaderright">
                        <span class="statusbadge status<?php echo $book['status']; ?>">
                            <?php 
                                $statusIcons = [
                                    'pending' => '⏳',
                                    'confirmed' => '✓',
                                    'completed' => '✅',
                                    'cancelled' => '❌',
                                    'rejected' => '🚫'
                                ];
                                echo $statusIcons[$book['status']] ?? '';
                            ?>
                            <?php echo ucfirst($book['status']); ?>
                        </span>
                        <span class="expandicon">▼</span>
                    </div>
                </div>

                <div class="bookingdetails" style="display: none;">
                    <div class="bookinginfo">
                        <div class="infoitem">
                            <span class="infoicon">📅</span>
                            <div class="infocontent">
                                <strong>Date & Time</strong>
                                <p><?php echo date('M d, Y', strtotime($book['scheduled_date'])); ?></p>
                                <p class="timetext"><?php echo date('h:i A', strtotime($book['scheduled_time'])); ?></p>
                            </div>
                        </div>
                        <div class="infoitem">
                            <span class="infoicon">⏱️</span>
                            <div class="infocontent">
                                <strong>Duration</strong>
                                <p><?php echo $book['duration']; ?> hours</p>
                            </div>
                        </div>
                        <div class="infoitem">
                            <span class="infoicon">💰</span>
                            <div class="infocontent">
                                <strong>Total Amount</strong>
                                <p class="priceinfo">₹<?php echo number_format($book['total_amount'], 2); ?></p>
                            </div>
                        </div>
                    </div>

                    <?php if ($book['status'] == 'completed' && $book['review_id']): ?>
                        <div class="reviewbox">
                            <div class="reviewheader">
                                <span class="reviewicon">💬</span>
                                <strong>Your Review</strong>
                            </div>
                            <div class="ratingdisplay">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <span class="ratingstar <?php echo $i <= $book['rating'] ? 'filled' : 'empty'; ?>">★</span>
                                <?php endfor; ?>
                                <span class="ratingvalue"><?php echo $book['rating']; ?>/5</span>
                            </div>
                            <?php if($book['comment']): ?>
                                <p class="reviewcomment"><?php echo nl2br(htmlspecialchars($book['comment'])); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="bookingactions">
                        <?php if ($book['status'] == 'pending' || $book['status'] == 'confirmed'): ?>
                            <a href="update_booking.php?id=<?php echo $book['id']; ?>&status=cancelled" class="btndanger" onclick="return confirm('Cancel this booking?')">
                                ❌ Cancel Booking
                            </a>
                        <?php endif; ?>

                        <?php if ($book['status'] == 'completed'): ?>
                            <?php if ($book['review_id']): ?>
                                <a href="leave_review.php?booking_id=<?php echo $book['id']; ?>&tech_id=<?php echo $book['technician_id']; ?>" class="btnprimary">
                                    ✏️ Edit Review
                                </a>
                            <?php else: ?>
                                <a href="leave_review.php?booking_id=<?php echo $book['id']; ?>&tech_id=<?php echo $book['technician_id']; ?>" class="btnsuccess">
                                    ⭐ Leave Review
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="emptybookings">
            <div class="emptyicon">📋</div>
            <h3>No bookings yet</h3>
            <p>Start booking services from our talented technicians</p>
            <a href="search.php" class="btnprimary">🔍 Find Technicians</a>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleBooking(headerElement) {
    const bookingCard = headerElement.closest('.bookingcard');
    const details = bookingCard.querySelector('.bookingdetails');
    const expandIcon = headerElement.querySelector('.expandicon');
    
    if (details.style.display === 'none') {
        details.style.display = 'block';
        expandIcon.textContent = '▲';
        bookingCard.classList.add('expanded');
    } else {
        details.style.display = 'none';
        expandIcon.textContent = '▼';
        bookingCard.classList.remove('expanded');
    }
}
</script>

<?php include 'includes/footer.php'; ?>
