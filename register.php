<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
include 'includes/header.php';
include 'config/db.php';

$cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

$cities = ["Mumbai", "Delhi", "Bangalore", "Hyderabad", "Ahmedabad", "Chennai", "Kolkata",
  "Pune", "Jaipur", "Surat", "Lucknow", "Kanpur", "Nagpur", "Indore", "Thane",
  "Bhopal", "Visakhapatnam", "Pimpri-Chinchwad", "Patna", "Vadodara", "Ghaziabad",
  "Ludhiana", "Agra", "Nashik", "Faridabad", "Meerut", "Rajkot", "Varanasi",
  "Srinagar", "Sambalpur"];

$states = ["Maharashtra", "Delhi", "Karnataka", "Telangana", "Gujarat", "Tamil Nadu", "West Bengal",
           "Rajasthan", "Uttar Pradesh", "Madhya Pradesh", "Andhra Pradesh", "Punjab", "Haryana", "Odisha", "Jammu and Kashmir"];
?>

<div class="formcontainer">
    <h2>Register</h2>

    <form method="POST" action="register_process.php">
        <div class="formgroup">
            <div style="display:flex;gap:15px;margin-top:10px;">
                <div class="rolebox active" onclick="selectrole('user')" id="userbox">
                    <input type="radio" name="roletype" value="user" id="usertype" checked onchange="hideshow()" style="display:none;">
                    <div style="font-size:32px;margin-bottom:8px;">👤</div>
                    <div style="font-weight:bold;font-size:17px;">User</div>
                    <div style="font-size:12px;color:#7f8c8d;margin-top:4px;">I need services</div>
                </div>
                <div class="rolebox" onclick="selectrole('technician')" id="techbox">
                    <input type="radio" name="roletype" value="technician" id="techtype" onchange="hideshow()" style="display:none;">
                    <div style="font-size:32px;margin-bottom:8px;">🔧</div>
                    <div style="font-weight:bold;font-size:17px;">Technician</div>
                    <div style="font-size:12px;color:#7f8c8d;margin-top:4px;">I provide services</div>
                </div>
            </div>
        </div>

        <div class="formgroup">
            <label>Name</label>
            <input type="text" name="uname" required>
        </div>

        <div class="formgroup">
            <label>Email</label>
            <input type="email" name="uemail" required>
        </div>

        <div class="formgroup">
            <label>Password</label>
            <input type="password" name="upass" required>
        </div>

        <div id="techfields" class="hidediv">
            <div class="formgroup">
                <label>Category</label>
                <select name="catid">
                    <option value="">Select Category</option>
                    <?php while($cat = mysqli_fetch_assoc($cats)): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="formgroup">
                <label>City</label>
                <select name="tcity">
                    <option value="">Select City</option>
                    <?php foreach($cities as $city): ?>
                        <option value="<?php echo $city; ?>"><?php echo $city; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="formgroup">
                <label>State</label>
                <select name="tstate">
                    <option value="">Select State</option>
                    <?php foreach($states as $state): ?>
                        <option value="<?php echo $state; ?>"><?php echo $state; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="formgroup">
                <label>Phone</label>
                <input type="text" name="tphone">
            </div>

            <div class="formgroup">
                <label>Bio</label>
                <textarea name="tbio"></textarea>
            </div>

            <div class="formgroup">
                <label>Skills (comma separated)</label>
                <input type="text" name="tskills" placeholder="e.g. Plumbing, Pipe Fitting">
            </div>

            <div class="formgroup">
                <label>Experience (years)</label>
                <input type="number" name="texp">
            </div>

            <div class="formgroup">
                <label>Hourly Rate (₹)</label>
                <input type="number" step="0.01" name="trate">
            </div>
        </div>

        <button type="submit" class="btnprimary">Register</button>
    </form>

    <p style="margin-top:20px;">Already have an account? <a href="login.php">Login here</a></p>
</div>

<script>
function selectrole(role) {
    var userbox = document.getElementById('userbox');
    var techbox = document.getElementById('techbox');
    var userradio = document.getElementById('usertype');
    var techradio = document.getElementById('techtype');

    if (role == 'user') {
        userbox.className = 'rolebox active';
        techbox.className = 'rolebox';
        userradio.checked = true;
    } else {
        userbox.className = 'rolebox';
        techbox.className = 'rolebox active';
        techradio.checked = true;
    }

    hideshow();
}

function hideshow() {
    var r = document.querySelector('input[name="roletype"]:checked').value;
    var div = document.getElementById('techfields');
    if (r == 'technician') {
        div.className = 'showdiv';
    } else {
        div.className = 'hidediv';
    }
}
</script>

<?php include 'includes/footer.php'; ?>
