<?php
require_once 'auth.php';
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports | Green Acres</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #27ae60; --dark: #2c3e50; --light: #f4f7f6; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--light); margin: 0; display: flex; }
        .sidebar { width: 260px; background: var(--dark); height: 100vh; position: fixed; color: white; }
        .sidebar h2 { text-align: center; padding: 20px 0; border-bottom: 1px solid #34495e; color: #2ecc71; }
        .sidebar a { color: white; text-decoration: none; padding: 15px 25px; display: block; }
        .sidebar a:hover { background: #34495e; border-left: 4px solid var(--primary); }
        .main-content { margin-left: 260px; padding: 40px; width: 100%; }
        
        .report-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px; }
        .report-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; }
        .report-card i { font-size: 40px; color: var(--primary); margin-bottom: 15px; }
        .btn-download { display: inline-block; background: var(--primary); color: white; text-decoration: none; padding: 12px 25px; border-radius: 6px; font-weight: 600; margin-top: 15px; }
        .btn-download:hover { background: #219150; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Green Acres</h2>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="animals.php"><i class="fas fa-cow"></i> Animals</a>
        <a href="crops.php"><i class="fas fa-leaf"></i> Crops</a>
        <a href="produce.php"><i class="fas fa-basket-shopping"></i> Produce</a>
        <a href="staff.php"><i class="fas fa-users"></i> Staff</a>
        <a href="reports.php" style="background:#34495e;"><i class="fas fa-file-pdf"></i> Reports</a>
        <a href="logout.php" style="color: #e74c3c; margin-top: 50px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <h1>Farm Reports & Analytics</h1>
        <p>Generate and download professional PDF reports for your farm records.</p>

        <div class="report-grid">
            <div class="report-card">
                <i class="fas fa-cow"></i>
                <h3>Livestock Inventory</h3>
                <p>Full list of all animals and current quantities.</p>
                <a href="generate_pdf.php?type=animals" class="btn-download"><i class="fas fa-download"></i> Download PDF</a>
            </div>

            <div class="report-card">
                <i class="fas fa-leaf"></i>
                <h3>Crop Harvest Report</h3>
                <p>Summary of all crops currently in stock.</p>
                <a href="generate_pdf.php?type=crops" class="btn-download"><i class="fas fa-download"></i> Download PDF</a>
            </div>

            <div class="report-card">
                <i class="fas fa-users"></i>
                <h3>Staff Directory</h3>
                <p>List of all registered farm personnel and roles.</p>
                <a href="generate_pdf.php?type=staff" class="btn-download"><i class="fas fa-download"></i> Download PDF</a>
            </div>
        </div>
    </div>
</body>
</html>