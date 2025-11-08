<?php
include 'includes/header.php';
include 'config/db.php';

$tid = $_GET['id'];

$techq = mysqli_query($conn, "SELECT t.*, u.name, c.name as category_name
                               FROM technicians t
                               JOIN users u ON t.user_id = u.id
                               JOIN categories c ON t.category_id = c.id
                               WHERE t.id = $tid");

if (mysqli_num_rows($techq) == 0) {
    echo "<p>Technician not found.</p>";
    include 'includes/footer.php';
    exit();
}

$tech = mysqli_fetch_assoc($techq);

$reviews = mysqli_query($conn, "SELECT r.*, u.name
                                FROM reviews r
                                JOIN users u ON r.user_id = u.id
                                WHERE r.technician_id = $tid
                                ORDER BY r.created_at DESC");
?>

<div class="profilepage">
    <div class="profileheader">
        <h1><?php echo htmlspecialchars($tech['name']); ?></h1>
        <p style="font-size:18px;color:#7f8c8d;"><?php echo htmlspecialchars($tech['category_name']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($tech['city']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($tech['phone']); ?></p>
        <p><strong>Rating:</strong> <span class="ratingstar">★</span> <?php echo number_format($tech['average_rating'], 2); ?></p>
        <p class="priceinfo" style="font-size:24px;">₹<?php echo number_format($tech['hourly_rate'], 2); ?>/hr</p>
    </div>

    <div style="margin-bottom:20px;">
        <h3>About</h3>
        <p><?php echo nl2br(htmlspecialchars($tech['bio'])); ?></p>
    </div>

    <div style="margin-bottom:20px;">
        <h3>Skills</h3>
        <?php
        $skillsarr = explode(',', $tech['skills']);
        foreach ($skillsarr as $skill):
        ?>
            <span class="badgeitem"><?php echo htmlspecialchars(trim($skill)); ?></span>
        <?php endforeach; ?>
    </div>

    <div style="margin-bottom:20px;">
        <p><strong>Experience:</strong> <?php echo $tech['experience']; ?> years</p>
        <p><strong>Availability:</strong> <?php echo htmlspecialchars($tech['availability']); ?></p>
    </div>

    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user'): ?>
        <a href="book.php?tech_id=<?php echo $tech['id']; ?>" class="btnprimary">Book Service</a>
    <?php endif; ?>

    <hr style="margin:30px 0;">

    <div>
        <h3>Reviews (<?php echo mysqli_num_rows($reviews); ?>)</h3>

        <?php if (mysqli_num_rows($reviews) > 0): ?>
            <?php while($rev = mysqli_fetch_assoc($reviews)): ?>
                <div class="reviewitem">
                    <p><strong><?php echo htmlspecialchars($rev['name']); ?></strong>
                       - <span class="ratingstar">★</span> <?php echo $rev['rating']; ?>/5</p>
                    <p><?php echo nl2br(htmlspecialchars($rev['comment'])); ?></p>
                    <p style="font-size:12px;color:#7f8c8d;"><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reviews yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
