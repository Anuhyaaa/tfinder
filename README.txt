T-FINDER - LOCAL TECHNICIAN FINDER (INDIA)
===========================================

SETUP INSTRUCTIONS
------------------

1. START XAMPP
   - Open XAMPP Control Panel
   - Start Apache and MySQL

2. CREATE DATABASE
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Go to SQL tab
   - Copy and paste the entire contents of database.sql file
   - Click Go to execute

3. SEED THE DATABASE
   - Open browser and visit: http://localhost/t-finder/scripts/seeder.php
   - Wait for completion (will generate 1800 technicians)
   - This may take 1-2 minutes
   - Database will be populated with sample data

4. ACCESS THE APPLICATION
   - Homepage: http://localhost/t-finder/

TEST ACCOUNTS
-------------

Regular Users (Login as customer):
- Email: rahul@test.com | Password: password123
- Email: priya@test.com | Password: password123
- Email: amit@test.com | Password: password123

Technicians (Login as service provider):
- Generated accounts: Check seeder output or use format:
  Email: [firstname][lastname][number]@tech.com
  Password: password123
  Example: aaravsharma0@tech.com, password123

SERVICE CATEGORIES
------------------
❄️ AC Repair
🔌 Appliance Repair
🚗 Car Mechanic
🔨 Carpenter
⚡ Electrician
📱 Mobile Repair
🎨 Painter
🔧 Plumber

CITIES COVERED
--------------
Mumbai, Delhi, Bangalore, Hyderabad, Ahmedabad, Chennai, Kolkata,
Pune, Jaipur, Surat, Lucknow, Kanpur, Nagpur, Indore, Thane,
Bhopal, Visakhapatnam, Pimpri-Chinchwad, Patna, Vadodara, Ghaziabad,
Ludhiana, Agra, Nashik, Faridabad, Meerut, Rajkot, Varanasi,
Srinagar, Sambalpur

DATABASE STATISTICS
-------------------
- Categories: 8
- Regular Users: 5
- Technicians: ~1800 (created by seeder script)
- Reviews: 0 (empty initially)

FEATURES
--------

For Users:
- Search technicians by category and city (dropdown filters)
- View technician profiles and reviews
- Book services
- Manage bookings
- Leave reviews for completed services

For Technicians:
- Create profile with skills and rates
- View job requests
- Confirm or reject bookings
- Mark jobs as completed

CURRENCY
--------
All pricing is in Indian Rupees (₹)

TROUBLESHOOTING
---------------

If you get database connection errors:
- Make sure MySQL is running in XAMPP
- Check that database name is "tfinder"
- Verify database credentials in config/db.php

If pages don't load properly:
- Make sure Apache is running in XAMPP
- Check that files are in C:\xampp\htdocs\t-finder\
- Clear browser cache

If seeder takes too long:
- This is normal - generating 1800 records takes time
- Wait for success message before using the app
