<?php
include 'includes/auth.php';
include 'includes/header.php';
include 'config/db.php';

$uid = $_SESSION['user_id'];
$role = $_SESSION['role'];
?>

<?php if ($role == 'user'): ?>

    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>

    <?php
    $totalbookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM bookings WHERE user_id = $uid"))['cnt'];
    $pendingbookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM bookings WHERE user_id = $uid AND status = 'pending'"))['cnt'];
    $completedbookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM bookings WHERE user_id = $uid AND status = 'completed'"))['cnt'];
    ?>

    <div class="dashstats">
        <div class="statbox">
            <h3>Total Bookings</h3>
            <p><?php echo $totalbookings; ?></p>
        </div>
        <div class="statbox">
            <h3>Pending</h3>
            <p><?php echo $pendingbookings; ?></p>
        </div>
        <div class="statbox">
            <h3>Completed</h3>
            <p><?php echo $completedbookings; ?></p>
        </div>
    </div>

    <div style="margin-top:20px;">
        <a href="my_bookings.php" class="btnprimary">View My Bookings</a>
        <a href="search.php" class="btnsuccess">Find Technicians</a>
    </div>

<?php else: ?>

    <?php
    $techdata = mysqli_fetch_assoc(mysqli_query($conn, "SELECT t.*, c.name as catname FROM technicians t JOIN categories c ON t.category_id = c.id WHERE t.user_id = $uid"));
    $techid = $techdata['id'];

    $alljobs = mysqli_query($conn, "SELECT b.*, u.name as custname FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.technician_id = $techid ORDER BY b.scheduled_date DESC");

    $jobsarr = [];
    while($j = mysqli_fetch_assoc($alljobs)) {
        $jobsarr[] = $j;
    }

    $totalearnings = 0;
    $monthearnings = 0;
    $completedjobs = 0;
    $pendingjobs = 0;
    $confirmedjobs = 0;
    $totaljobs = count($jobsarr);
    $currentmonth = date('Y-m');
    $monthcount = 0;

    foreach($jobsarr as $job) {
        if($job['status'] == 'completed') {
            $totalearnings += $job['total_amount'];
            $completedjobs++;
        }
        if($job['status'] == 'pending') {
            $pendingjobs++;
        }
        if($job['status'] == 'confirmed') {
            $confirmedjobs++;
        }
        if(substr($job['scheduled_date'], 0, 7) == $currentmonth) {
            $monthcount++;
            if($job['status'] == 'completed') {
                $monthearnings += $job['total_amount'];
            }
        }
    }

    $avgjobval = $completedjobs > 0 ? $totalearnings / $completedjobs : 0;
    $successrate = $totaljobs > 0 ? ($completedjobs / $totaljobs) * 100 : 0;

    $recentjobs = array_slice($jobsarr, 0, 3);
    ?>

    <div class="techheader">
        <div class="techavatar">
            <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
        </div>
        <div class="techinfo">
            <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
            <p><?php echo htmlspecialchars($techdata['catname']); ?> • <?php echo $techdata['experience']; ?> years experience</p>
        </div>
        <div class="techactions">
            <a href="jobs.php" class="btnprimary">View Jobs</a>
        </div>
    </div>

    <div class="dashstats">
        <div class="statbox">
            <h3>Total Earnings</h3>
            <p>₹<?php echo number_format($totalearnings, 2); ?></p>
            <small><?php echo $completedjobs; ?> completed jobs</small>
        </div>
        <div class="statbox">
            <h3>This Month</h3>
            <p>₹<?php echo number_format($monthearnings, 2); ?></p>
            <small><?php echo $monthcount; ?> jobs this month</small>
        </div>
        <div class="statbox">
            <h3>Completed Jobs</h3>
            <p><?php echo $completedjobs; ?></p>
            <small>Avg: ₹<?php echo number_format($avgjobval, 2); ?></small>
        </div>
        <div class="statbox">
            <h3>Pending Jobs</h3>
            <p><?php echo $pendingjobs; ?></p>
            <small>Need action</small>
        </div>
    </div>

    <div class="dashgrid">
        <div class="dashpanel">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <h3>Recent Activity</h3>
                <a href="jobs.php" style="color:#2563eb;text-decoration:none;font-size:14px;">View All →</a>
            </div>

            <?php if(count($recentjobs) > 0): ?>
                <?php foreach($recentjobs as $rjob): ?>
                    <div class="activityitem">
                        <div>
                            <strong><?php echo htmlspecialchars($rjob['service']); ?></strong>
                            <p style="margin:5px 0;color:#7f8c8d;font-size:14px;">
                                <?php echo htmlspecialchars($rjob['custname']); ?> •
                                <?php echo date('M d, Y', strtotime($rjob['scheduled_date'])); ?>
                            </p>
                        </div>
                        <span class="statusbadge status<?php echo $rjob['status']; ?>">
                            <?php echo ucfirst($rjob['status']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:#7f8c8d;text-align:center;padding:20px;">No recent activity</p>
            <?php endif; ?>
        </div>

        <div class="dashpanel">
            <h3 style="margin-bottom:20px;">Performance</h3>
            <div class="perfitem">
                <span>Hourly Rate</span>
                <strong>₹<?php echo number_format($techdata['hourly_rate'], 2); ?></strong>
            </div>
            <div class="perfitem">
                <span>Success Rate</span>
                <strong><?php echo number_format($successrate, 1); ?>%</strong>
            </div>
            <div class="perfitem">
                <span>This Month</span>
                <strong><?php echo $monthcount; ?> jobs</strong>
            </div>
            <div class="perfitem">
                <span>Avg Job Value</span>
                <strong>₹<?php echo number_format($avgjobval, 2); ?></strong>
            </div>
            <div class="perfitem">
                <span>Active Jobs</span>
                <strong><?php echo $confirmedjobs; ?></strong>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
