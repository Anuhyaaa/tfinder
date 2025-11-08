<?php
include 'includes/header.php';
include 'config/db.php';

$cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

$cities = ["Mumbai", "Delhi", "Bangalore", "Hyderabad", "Ahmedabad", "Chennai", "Kolkata",
  "Pune", "Jaipur", "Surat", "Lucknow", "Kanpur", "Nagpur", "Indore", "Thane",
  "Bhopal", "Visakhapatnam", "Pimpri-Chinchwad", "Patna", "Vadodara", "Ghaziabad",
  "Ludhiana", "Agra", "Nashik", "Faridabad", "Meerut", "Rajkot", "Varanasi",
  "Srinagar", "Sambalpur"];

$sql = "SELECT t.*, u.name, c.name as category_name
        FROM technicians t
        JOIN users u ON t.user_id = u.id
        JOIN categories c ON t.category_id = c.id
        WHERE 1=1";

if (isset($_GET['service_query']) && $_GET['service_query'] != '') {
    $sq = $_GET['service_query'];
    $sql .= " AND (u.name LIKE '%$sq%' OR c.name LIKE '%$sq%' OR t.skills LIKE '%$sq%')";
}

if (isset($_GET['location_query']) && $_GET['location_query'] != '') {
    $lq = $_GET['location_query'];
    $sql .= " AND t.city LIKE '%$lq%'";
}

if (isset($_GET['category']) && $_GET['category'] != '') {
    $catid = $_GET['category'];
    $sql .= " AND t.category_id = $catid";
}

if (isset($_GET['location']) && $_GET['location'] != '') {
    $loc = $_GET['location'];
    $sql .= " AND t.city = '$loc'";
}

if (isset($_GET['min_rate']) && $_GET['min_rate'] != '') {
    $minr = $_GET['min_rate'];
    $sql .= " AND t.hourly_rate >= $minr";
}

if (isset($_GET['max_rate']) && $_GET['max_rate'] != '') {
    $maxr = $_GET['max_rate'];
    $sql .= " AND t.hourly_rate <= $maxr";
}

if (isset($_GET['rating']) && $_GET['rating'] != '') {
    $rat = $_GET['rating'];
    $sql .= " AND t.average_rating >= $rat";
}

$sortby = isset($_GET['sort']) ? $_GET['sort'] : 'rating_desc';

if ($sortby == 'price_asc') {
    $sql .= " ORDER BY t.hourly_rate ASC";
} elseif ($sortby == 'price_desc') {
    $sql .= " ORDER BY t.hourly_rate DESC";
} elseif ($sortby == 'exp_desc') {
    $sql .= " ORDER BY t.experience DESC";
} else {
    $sql .= " ORDER BY t.average_rating DESC";
}

$totalresult = mysqli_query($conn, $sql);
$totalcount = mysqli_num_rows($totalresult);

$showlimit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

$sql .= " LIMIT $showlimit";

$results = mysqli_query($conn, $sql);
$currentcount = mysqli_num_rows($results);
?>

<h2>Search Technicians</h2>

<div class="searchlayout">
    <div class="filtersidebar">
        <h3>Filters</h3>
        <form method="GET" action="search.php">
            <div class="formgroup">
                <label>Sort By</label>
                <select name="sort">
                    <option value="rating_desc" <?php echo ($sortby == 'rating_desc') ? 'selected' : ''; ?>>Highest Rated</option>
                    <option value="price_asc" <?php echo ($sortby == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php echo ($sortby == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="exp_desc" <?php echo ($sortby == 'exp_desc') ? 'selected' : ''; ?>>Most Experienced</option>
                </select>
            </div>

            <div class="formgroup">
                <label>Category</label>
                <select name="category">
                    <option value="">All Categories</option>
                    <?php while($cat = mysqli_fetch_assoc($cats)): ?>
                        <option value="<?php echo $cat['id']; ?>"
                            <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="formgroup">
                <label>Location</label>
                <select name="location">
                    <option value="">All Cities</option>
                    <?php foreach($cities as $city): ?>
                        <option value="<?php echo $city; ?>"
                            <?php echo (isset($_GET['location']) && $_GET['location'] == $city) ? 'selected' : ''; ?>>
                            <?php echo $city; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="formgroup">
                <label>Min Rate (₹)</label>
                <input type="number" name="min_rate" step="1"
                       value="<?php echo isset($_GET['min_rate']) ? htmlspecialchars($_GET['min_rate']) : ''; ?>">
            </div>

            <div class="formgroup">
                <label>Max Rate (₹)</label>
                <input type="number" name="max_rate" step="1"
                       value="<?php echo isset($_GET['max_rate']) ? htmlspecialchars($_GET['max_rate']) : ''; ?>">
            </div>

            <div class="formgroup">
                <label>Rating</label>
                <select name="rating">
                    <option value="">All</option>
                    <option value="4" <?php echo (isset($_GET['rating']) && $_GET['rating'] == 4) ? 'selected' : ''; ?>>4 Stars & Up</option>
                    <option value="3" <?php echo (isset($_GET['rating']) && $_GET['rating'] == 3) ? 'selected' : ''; ?>>3 Stars & Up</option>
                    <option value="2" <?php echo (isset($_GET['rating']) && $_GET['rating'] == 2) ? 'selected' : ''; ?>>2 Stars & Up</option>
                    <option value="1" <?php echo (isset($_GET['rating']) && $_GET['rating'] == 1) ? 'selected' : ''; ?>>1 Star & Up</option>
                </select>
            </div>

            <button type="submit" class="btnprimary">Apply Filters</button>
        </form>
    </div>

    <div class="resultsarea" id="results">
        <h3>Showing <?php echo $currentcount; ?> of <?php echo $totalcount; ?> technicians</h3>

        <?php if (mysqli_num_rows($results) > 0): ?>
            <?php while($tech = mysqli_fetch_assoc($results)): ?>
                <div class="techcard">
                    <h3><?php echo htmlspecialchars($tech['name']); ?></h3>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($tech['category_name']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($tech['city']); ?></p>
                    <p><strong>Skills:</strong> <?php echo htmlspecialchars($tech['skills']); ?></p>
                    <p><strong>Experience:</strong> <?php echo $tech['experience']; ?> years</p>
                    <p><strong>Rating:</strong> <span class="ratingstar">★</span> <?php echo number_format($tech['average_rating'], 1); ?></p>
                    <p class="priceinfo">₹<?php echo number_format($tech['hourly_rate'], 2); ?>/hr</p>
                    <a href="technician.php?id=<?php echo $tech['id']; ?>" class="btnprimary">View Profile</a>
                </div>
            <?php endwhile; ?>

            <?php if ($currentcount < $totalcount): ?>
                <div style="text-align:center;margin-top:30px;">
                    <form method="GET" action="search.php#results" style="display:inline;">
                        <?php if (isset($_GET['sort'])): ?>
                            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($_GET['sort']); ?>">
                        <?php endif; ?>
                        <?php if (isset($_GET['category'])): ?>
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($_GET['category']); ?>">
                        <?php endif; ?>
                        <?php if (isset($_GET['location'])): ?>
                            <input type="hidden" name="location" value="<?php echo htmlspecialchars($_GET['location']); ?>">
                        <?php endif; ?>
                        <?php if (isset($_GET['min_rate'])): ?>
                            <input type="hidden" name="min_rate" value="<?php echo htmlspecialchars($_GET['min_rate']); ?>">
                        <?php endif; ?>
                        <?php if (isset($_GET['max_rate'])): ?>
                            <input type="hidden" name="max_rate" value="<?php echo htmlspecialchars($_GET['max_rate']); ?>">
                        <?php endif; ?>
                        <?php if (isset($_GET['rating'])): ?>
                            <input type="hidden" name="rating" value="<?php echo htmlspecialchars($_GET['rating']); ?>">
                        <?php endif; ?>
                        <input type="hidden" name="limit" value="<?php echo $showlimit + 5; ?>">
                        <button type="submit" class="btnprimary">Load More</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p>No technicians found matching your criteria.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
