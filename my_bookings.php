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

$bookings = mysqli_query($conn, "SELECT b.*, u.name as tech_name
                                  FROM bookings b
                                  JOIN technicians t ON b.technician_id = t.id
                                  JOIN users u ON t.user_id = u.id
                                  WHERE b.user_id = $uid
                                  ORDER BY b.scheduled_date DESC");
?>

<h2>My Bookings</h2>

<div class="tablebox">
    <?php if (mysqli_num_rows($bookings) > 0): ?>
        <table class="datatable">
            <thead>
                <tr>
                    <th>Technician</th>
                    <th>Service</th>
                    <th>Date & Time</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($book = mysqli_fetch_assoc($bookings)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['tech_name']); ?></td>
                        <td><?php echo htmlspecialchars($book['service']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($book['scheduled_date'])); ?> at <?php echo date('h:i A', strtotime($book['scheduled_time'])); ?></td>
                        <td>₹<?php echo number_format($book['total_amount'], 2); ?></td>
                        <td><span class="statusbadge status<?php echo $book['status']; ?>"><?php echo ucfirst($book['status']); ?></span></td>
                        <td>
                            <?php if ($book['status'] == 'pending' || $book['status'] == 'confirmed'): ?>
                                <a href="update_booking.php?id=<?php echo $book['id']; ?>&status=cancelled" class="btndanger" onclick="return confirm('Cancel this booking?')">Cancel</a>
                            <?php endif; ?>

                            <?php if ($book['status'] == 'completed'): ?>
                                <a href="leave_review.php?booking_id=<?php echo $book['id']; ?>&tech_id=<?php echo $book['technician_id']; ?>" class="btnsuccess">Leave Review</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No bookings yet.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
