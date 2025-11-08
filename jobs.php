<?php
include 'includes/auth.php';
include 'includes/header.php';
include 'config/db.php';

if ($_SESSION['role'] != 'technician') {
    echo "<p>Access denied.</p>";
    include 'includes/footer.php';
    exit();
}

$uid = $_SESSION['user_id'];
$techid = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM technicians WHERE user_id = $uid"))['id'];

$alljobs = mysqli_query($conn, "SELECT b.*, u.name as custname
                              FROM bookings b
                              JOIN users u ON b.user_id = u.id
                              WHERE b.technician_id = $techid
                              ORDER BY b.scheduled_date DESC");

$jobsarr = [];
while($j = mysqli_fetch_assoc($alljobs)) {
    $jobsarr[] = $j;
}

$currentfilter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$filteredjobs = [];
$countall = count($jobsarr);
$countpending = 0;
$countconfirmed = 0;
$countcompleted = 0;
$countcancelled = 0;

foreach($jobsarr as $job) {
    if($job['status'] == 'pending') $countpending++;
    if($job['status'] == 'confirmed') $countconfirmed++;
    if($job['status'] == 'completed') $countcompleted++;
    if($job['status'] == 'cancelled' || $job['status'] == 'rejected') $countcancelled++;

    if($currentfilter == 'all') {
        $filteredjobs[] = $job;
    } else if($currentfilter == 'cancelled') {
        if($job['status'] == 'cancelled' || $job['status'] == 'rejected') {
            $filteredjobs[] = $job;
        }
    } else {
        if($job['status'] == $currentfilter) {
            $filteredjobs[] = $job;
        }
    }
}
?>

<h2>My Jobs</h2>

<div class="filtertabs">
    <a href="jobs.php?filter=all" class="tabitm <?php echo $currentfilter == 'all' ? 'active' : ''; ?>">
        All <span class="tabcount"><?php echo $countall; ?></span>
    </a>
    <a href="jobs.php?filter=pending" class="tabitm <?php echo $currentfilter == 'pending' ? 'active' : ''; ?>">
        Pending <span class="tabcount"><?php echo $countpending; ?></span>
    </a>
    <a href="jobs.php?filter=confirmed" class="tabitm <?php echo $currentfilter == 'confirmed' ? 'active' : ''; ?>">
        Confirmed <span class="tabcount"><?php echo $countconfirmed; ?></span>
    </a>
    <a href="jobs.php?filter=completed" class="tabitm <?php echo $currentfilter == 'completed' ? 'active' : ''; ?>">
        Completed <span class="tabcount"><?php echo $countcompleted; ?></span>
    </a>
    <a href="jobs.php?filter=cancelled" class="tabitm <?php echo $currentfilter == 'cancelled' ? 'active' : ''; ?>">
        Cancelled <span class="tabcount"><?php echo $countcancelled; ?></span>
    </a>
</div>

<div class="jobslist">
    <?php if (count($filteredjobs) > 0): ?>
        <?php foreach($filteredjobs as $job): ?>
            <div class="jobcard">
                <div class="jobheader" onclick="togglejob(<?php echo $job['id']; ?>)">
                    <div class="jobmain">
                        <h3><?php echo htmlspecialchars($job['service']); ?></h3>
                        <div class="jobmeta">
                            <span>📅 <?php echo date('M d, Y', strtotime($job['scheduled_date'])); ?></span>
                            <span>🕐 <?php echo date('h:i A', strtotime($job['scheduled_time'])); ?></span>
                            <span>💰 ₹<?php echo number_format($job['total_amount'], 2); ?></span>
                        </div>
                    </div>
                    <div class="jobstatus">
                        <span class="statusbadge status<?php echo $job['status']; ?>">
                            <?php echo ucfirst($job['status']); ?>
                        </span>
                        <span class="expandicon" id="icon<?php echo $job['id']; ?>">▼</span>
                    </div>
                </div>

                <div class="jobdetails hidediv" id="details<?php echo $job['id']; ?>">
                    <div class="detailgrid">
                        <div class="detailitem">
                            <strong>Customer</strong>
                            <p><?php echo htmlspecialchars($job['custname']); ?></p>
                        </div>
                        <div class="detailitem">
                            <strong>Contact Phone</strong>
                            <p><?php echo htmlspecialchars($job['contact_phone']); ?></p>
                        </div>
                        <div class="detailitem">
                            <strong>Duration</strong>
                            <p><?php echo $job['duration']; ?> hours</p>
                        </div>
                        <div class="detailitem">
                            <strong>Amount</strong>
                            <p>₹<?php echo number_format($job['total_amount'], 2); ?></p>
                        </div>
                    </div>

                    <?php if($job['description']): ?>
                        <div class="detailsection">
                            <strong>Description</strong>
                            <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="detailsection">
                        <strong>Service Address</strong>
                        <p><?php echo htmlspecialchars($job['address_street']); ?>,
                           <?php echo htmlspecialchars($job['address_city']); ?>,
                           <?php echo htmlspecialchars($job['address_state']); ?> -
                           <?php echo htmlspecialchars($job['address_pincode']); ?></p>
                    </div>

                    <?php if($job['notes']): ?>
                        <div class="detailsection">
                            <strong>Additional Notes</strong>
                            <p><?php echo nl2br(htmlspecialchars($job['notes'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="jobactions">
                        <?php if ($job['status'] == 'pending'): ?>
                            <a href="update_booking.php?id=<?php echo $job['id']; ?>&status=confirmed&return=jobs" class="btnsuccess" onclick="return confirm('Accept this job?')">Accept Job</a>
                            <a href="update_booking.php?id=<?php echo $job['id']; ?>&status=rejected&return=jobs" class="btndanger" onclick="return confirm('Reject this job?')">Reject</a>
                        <?php endif; ?>

                        <?php if ($job['status'] == 'confirmed'): ?>
                            <a href="update_booking.php?id=<?php echo $job['id']; ?>&status=completed&return=jobs" class="btnsuccess" onclick="return confirm('Mark as completed?')">Mark Complete</a>
                        <?php endif; ?>

                        <?php if ($job['status'] == 'pending' || $job['status'] == 'confirmed'): ?>
                            <a href="update_booking.php?id=<?php echo $job['id']; ?>&status=cancelled&return=jobs" class="btnwarning" onclick="return confirm('Cancel this job?')">Cancel</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align:center;padding:40px;color:#7f8c8d;">
            <p>No <?php echo $currentfilter == 'all' ? '' : $currentfilter; ?> jobs found.</p>
        </div>
    <?php endif; ?>
</div>

<script>
function togglejob(jobid) {
    var details = document.getElementById('details' + jobid);
    var icon = document.getElementById('icon' + jobid);

    if (details.className == 'jobdetails hidediv') {
        details.className = 'jobdetails showdiv';
        icon.innerHTML = '▲';
    } else {
        details.className = 'jobdetails hidediv';
        icon.innerHTML = '▼';
    }
}
</script>

<?php include 'includes/footer.php'; ?>
