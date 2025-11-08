<?php
include '../config/db.php';

echo "<h2>Database Seeder</h2>";

$catfile = 'data/categories.json';
$userfile = 'data/users_regular.json';
$citiesfile = 'data/cities.json';

if (!file_exists($catfile) || !file_exists($userfile) || !file_exists($citiesfile)) {
    die("Error: JSON data files not found in scripts/data/ directory");
}

$catjson = file_get_contents($catfile);
$userjson = file_get_contents($userfile);
$citiesjson = file_get_contents($citiesfile);

$cats = json_decode($catjson, true);
$users = json_decode($userjson, true);
$cities = json_decode($citiesjson, true);

$firstnames = ["Aarav", "Vivaan", "Aditya", "Vihaan", "Arjun", "Sai", "Arnav", "Ayaan", "Krishna", "Ishaan",
               "Shaurya", "Atharv", "Advik", "Pranav", "Reyansh", "Aaradhya", "Ananya", "Pari", "Anika", "Sara",
               "Diya", "Ira", "Navya", "Kiara", "Prisha", "Ravi", "Suresh", "Ramesh", "Vijay", "Prakash",
               "Rajesh", "Amit", "Rahul", "Rohan", "Karan", "Nikhil", "Varun", "Arun", "Mohan", "Gopal",
               "Deepak", "Ankit", "Manish", "Pankaj", "Ashok", "Sanjay", "Manoj", "Vinod", "Ajay", "Pradeep",
               "Dinesh", "Mukesh", "Yogesh", "Lokesh", "Santosh", "Mahesh", "Ganesh", "Naresh", "Sunil", "Anil",
               "Vishal", "Kunal", "Harsh", "Akash", "Aman", "Dev", "Naman", "Rishabh", "Yash", "Tanvi",
               "Sneha", "Pooja", "Priya", "Neha", "Kavya", "Riya", "Shruti", "Simran", "Divya", "Ishita",
               "Megha", "Swati", "Pallavi", "Shweta", "Sapna", "Ritu", "Seema", "Geeta", "Sunita", "Anita",
               "Rekha", "Usha", "Maya", "Radha", "Anjali", "Bhavna", "Komal", "Nisha", "Payal", "Preeti"];

$lastnames = ["Sharma", "Verma", "Patel", "Kumar", "Singh", "Reddy", "Gupta", "Joshi", "Iyer", "Nair",
              "Desai", "Kulkarni", "Rao", "Mehta", "Shah", "Jain", "Agarwal", "Bansal", "Goyal", "Arora",
              "Malhotra", "Khanna", "Bhatia", "Chopra", "Kapoor", "Chauhan", "Rathore", "Thakur", "Pandey", "Mishra",
              "Tiwari", "Dubey", "Shukla", "Saxena", "Srivastava", "Yadav", "Chaudhary", "Bhatt", "Jha", "Pillai",
              "Menon", "Das", "Bose", "Ghosh", "Mukherjee", "Chatterjee", "Roy", "Dutta", "Sen", "Banerjee",
              "Sahu", "Patil", "Pawar", "Jadhav", "Deshpande", "Wagh", "Mahajan", "Shinde", "Kadam", "Mane"];

$states = ["Maharashtra", "Delhi", "Karnataka", "Telangana", "Gujarat", "Tamil Nadu", "West Bengal",
           "Rajasthan", "Uttar Pradesh", "Madhya Pradesh", "Andhra Pradesh", "Punjab", "Haryana", "Odisha", "Jammu and Kashmir"];

$skillsbycat = [
    "AC Repair" => ["Split AC Repair", "Window AC Repair", "AC Installation", "AC Servicing", "Gas Refilling", "Compressor Repair"],
    "Appliance Repair" => ["Washing Machine Repair", "Refrigerator Repair", "Microwave Repair", "TV Repair", "Dishwasher Repair", "Chimney Repair"],
    "Car Mechanic" => ["Engine Repair", "Brake Service", "Oil Change", "Battery Replacement", "Tire Replacement", "AC Service"],
    "Carpenter" => ["Furniture Making", "Door Installation", "Window Repair", "Cabinet Making", "Wooden Flooring", "Custom Woodwork"],
    "Electrician" => ["Wiring", "Switch Board Installation", "Fan Installation", "Light Fitting", "Electrical Repairs", "MCB Installation"],
    "Mobile Repair" => ["Screen Replacement", "Battery Replacement", "Charging Port Repair", "Software Issues", "Water Damage Repair", "Speaker Repair"],
    "Painter" => ["Interior Painting", "Exterior Painting", "Texture Painting", "Wall Putty", "Waterproofing", "Wood Polish"],
    "Plumber" => ["Pipe Fitting", "Leak Repair", "Tap Installation", "Bathroom Fitting", "Drainage Cleaning", "Tank Installation"]
];

$biotemps = [
    "Experienced professional with expertise in %s. Serving %s area with dedication.",
    "Certified %s specialist providing quality service in %s and nearby areas.",
    "Skilled %s technician with %d years of experience. Available in %s.",
    "Professional %s services in %s. Quick response and reliable work.",
    "Expert %s professional serving %s with excellent customer satisfaction.",
    "Trusted %s specialist in %s area. Quality work guaranteed.",
    "Reliable %s services for residential and commercial needs in %s."
];

echo "<p>Clearing database...</p>";

mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($conn, "TRUNCATE TABLE reviews");
mysqli_query($conn, "TRUNCATE TABLE bookings");
mysqli_query($conn, "TRUNCATE TABLE technicians");
mysqli_query($conn, "TRUNCATE TABLE users");
mysqli_query($conn, "TRUNCATE TABLE categories");
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

echo "<p>Seeding categories...</p>";

$catmap = array();
foreach ($cats as $cat) {
    $cname = $cat['name'];
    mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$cname')");
    $catid = mysqli_insert_id($conn);
    $catmap[$cname] = $catid;
}

echo "<p>Categories seeded: " . count($cats) . "</p>";

echo "<p>Seeding regular users...</p>";

foreach ($users as $usr) {
    $uname = $usr['name'];
    $uemail = $usr['email'];
    $upass = password_hash($usr['password'], PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('$uname', '$uemail', '$upass', 'user')");
}

echo "<p>Users seeded: " . count($users) . "</p>";

echo "<p>Generating technicians...</p>";
flush();

$totaltechs = 0;
$targetcount = 1800;

while ($totaltechs < $targetcount) {
    $fname = $firstnames[array_rand($firstnames)];
    $lname = $lastnames[array_rand($lastnames)];
    $fullname = $fname . " " . $lname;

    $emailname = strtolower($fname . $lname . $totaltechs);
    $temail = $emailname . "@tech.com";

    $tpass = password_hash("password123", PASSWORD_DEFAULT);

    $catname = array_keys($catmap)[array_rand(array_keys($catmap))];
    $catid = $catmap[$catname];

    $tcity = $cities[array_rand($cities)];
    $tstate = $states[array_rand($states)];

    $tphone = "9" . rand(100000000, 999999999);

    $tbio = sprintf($biotemps[array_rand($biotemps)], $catname, $tcity, rand(3, 20), $tcity);
    $tbio = mysqli_real_escape_string($conn, $tbio);

    $catskills = $skillsbycat[$catname];
    shuffle($catskills);
    $numskills = rand(3, 5);
    $selectedskills = array_slice($catskills, 0, $numskills);
    $tskills = implode(',', $selectedskills);

    $texp = rand(2, 20);
    $trate = rand(20, 150) * 10;

    mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('$fullname', '$temail', '$tpass', 'technician')");
    $newuid = mysqli_insert_id($conn);

    mysqli_query($conn, "INSERT INTO technicians (user_id, category_id, city, state, phone, bio, skills, experience, hourly_rate)
                         VALUES ($newuid, $catid, '$tcity', '$tstate', '$tphone', '$tbio', '$tskills', $texp, $trate)");

    $totaltechs++;

    if ($totaltechs % 100 == 0) {
        echo "<p>Generated $totaltechs technicians...</p>";
        flush();
    }
}

echo "<h3 style='color:green;'>Database Seeded Successfully!</h3>";
echo "<p>Categories: " . count($cats) . "</p>";
echo "<p>Regular Users: " . count($users) . "</p>";
echo "<p>Technicians: $totaltechs</p>";
echo "<p>Reviews table is empty.</p>";
echo "<p><a href='../index.php'>Go to Homepage</a></p>";
?>
