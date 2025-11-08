<?php
include 'includes/header.php';
include 'config/db.php';

$cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
$featured = mysqli_query($conn, "SELECT t.*, u.name, c.name as category_name
                                  FROM technicians t
                                  JOIN users u ON t.user_id = u.id
                                  JOIN categories c ON t.category_id = c.id
                                  ORDER BY t.average_rating DESC
                                  LIMIT 6");

$cities = ["Mumbai", "Delhi", "Bangalore", "Hyderabad", "Ahmedabad", "Chennai", "Kolkata",
  "Pune", "Jaipur", "Surat", "Lucknow", "Kanpur", "Nagpur", "Indore", "Thane",
  "Bhopal", "Visakhapatnam", "Pimpri-Chinchwad", "Patna", "Vadodara", "Ghaziabad",
  "Ludhiana", "Agra", "Nashik", "Faridabad", "Meerut", "Rajkot", "Varanasi",
  "Srinagar", "Sambalpur"];
?>

<div class="bigheader">
    <h1>Find Local Technicians Near You</h1>
    <p>Connect with skilled professionals for all your service needs</p>

    <div class="searchbox">
        <form method="GET" action="search.php" class="searchform">
            <select name="category" style="flex:1;min-width:200px;padding:12px;border:1px solid #ddd;border-radius:4px;font-size:14px;">
                <option value="">Select Service Category</option>
                <?php
                mysqli_data_seek($cats, 0);
                while($cat = mysqli_fetch_assoc($cats)):
                ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endwhile; ?>
            </select>

            <select name="location" style="flex:1;min-width:200px;padding:12px;border:1px solid #ddd;border-radius:4px;font-size:14px;">
                <option value="">Select City</option>
                <?php foreach($cities as $city): ?>
                    <option value="<?php echo $city; ?>"><?php echo $city; ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btnprimary">Search</button>
        </form>
    </div>
</div>

<div class="gridsection">
    <h2>Popular Services</h2>
    <div class="cardgrid">
        <?php
        mysqli_data_seek($cats, 0);
        while($cat = mysqli_fetch_assoc($cats)):
        ?>
            <a href="search.php?category=<?php echo $cat['id']; ?>" class="carditem">
                <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                <p>Find <?php echo strtolower(htmlspecialchars($cat['name'])); ?>s in your area</p>
            </a>
        <?php endwhile; ?>
    </div>
</div>

<div class="gridsection">
    <h2>Featured Technicians</h2>
    <div class="cardgrid">
        <?php while($tech = mysqli_fetch_assoc($featured)): ?>
            <a href="technician.php?id=<?php echo $tech['id']; ?>" class="carditem">
                <h3><?php echo htmlspecialchars($tech['name']); ?></h3>
                <p><?php echo htmlspecialchars($tech['category_name']); ?></p>
                <p><?php echo htmlspecialchars($tech['city']); ?></p>
                <p>
                    <span class="ratingstar">★</span>
                    <?php echo number_format($tech['average_rating'], 1); ?>
                </p>
                <p class="priceinfo">₹<?php echo number_format($tech['hourly_rate'], 2); ?>/hr</p>
            </a>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
