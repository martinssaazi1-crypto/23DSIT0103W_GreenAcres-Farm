<?php
include "auth.php";
include "db.php";

// Fetch counts for dashboard
$animals = $conn->query("SELECT COUNT(*) AS total FROM animals")->fetch_assoc()['total'];
$crops   = $conn->query("SELECT COUNT(*) AS total FROM crops")->fetch_assoc()['total'];
$staff   = $conn->query("SELECT COUNT(*) AS total FROM staff")->fetch_assoc()['total'];
$produce = $conn->query("SELECT COUNT(*) AS total FROM produce")->fetch_assoc()['total'];

// Example analytics: percentage of animals vs total produce
$totalItems = $animals + $crops + $produce;
$animalPercent = $totalItems ? round(($animals / $totalItems) * 100, 1) : 0;
$cropPercent   = $totalItems ? round(($crops / $totalItems) * 100, 1) : 0;
$producePercent = $totalItems ? round(($produce / $totalItems) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Farm Dashboard</title>
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

/* ================= GLOBAL ================= */
body{
    font-family: Arial, Helvetica, sans-serif;
    margin:0;
    background: linear-gradient(to right, #d4f1f4, #ffffff);
    color: #333;
}

/* ================= HEADER ================= */
header{
    background:#66CDAA;
    color:white;
    padding:15px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 10px rgba(0,0,0,0.2);
}

header .logo{
    display:flex;
    align-items:center;
    gap:15px;
}

header .logo img{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:10px;
    border:2px solid white;
}

header h2{
    margin:0;
    color:#ff7a00; /* ORANGE TEXT */
    font-size:28px;
    letter-spacing:1px;
}

header h2 span{
    color:#ff0000; /* RED TEXT */
}

/* ================= DASHBOARD CONTAINER ================= */
.dashboard{
    padding:30px;
}

/* ================= CARDS ================= */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
}

.card{
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 6px 15px rgba(0,0,0,0.2);
    text-align:center;
    transition: transform 0.3s, box-shadow 0.3s;
    position:relative;
    overflow:hidden;
}

.card::after{
    content:'';
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:5px;
    background:#ff7a00; /* ORANGE LINE */
}

.card i{
    font-size:40px;
    color:#ff7a00; /* ORANGE ICON */
    margin-bottom:10px;
}

.card h2{
    margin:10px 0;
    font-size:28px;
    color:#ff0000; /* RED NUMBER */
}

.card p{
    font-size:16px;
    color:#555;
    margin-bottom:10px;
}

.card .analysis{
    font-size:14px;
    color:#ff7a00; /* ORANGE ANALYSIS */
    font-weight:bold;
}

/* ================= QUICK LINKS ================= */
.quick-links{
    margin-top:40px;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:20px;
}

.link{
    background:#66CDAA;
    color:white;
    padding:20px;
    border-radius:15px;
    text-align:center;
    text-decoration:none;
    font-weight:bold;
    transition: 0.3s;
    box-shadow:0 4px 12px rgba(0,0,0,0.2);
}

.link i{
    font-size:30px;
    margin-bottom:10px;
    color:#ff7a00; /* ORANGE ICON */
}

.link:hover{
    background:#ff0000; /* RED HOVER */
    transform: translateY(-3px);
}

/* ================= RESPONSIVE ================= */
@media(max-width:600px){
header{
    flex-direction:column;
    align-items:flex-start;
    gap:10px;
}

.cards{
    grid-template-columns:1fr;
}

.quick-links{
    grid-template-columns:1fr;
}
}
</style>
</head>

<body>

<header>
<div class="logo">
<img src="assets/images/logo.jpg" alt="Company Logo">
<h2>GREEN ACRES <span>Dashboard</span></h2>
</div>

<div>
<a href="index.php" style="color:#ff7a00;text-decoration:none;font-weight:bold;">
Green Acres Farm Profile
</a>
</div>
</header>

<div class="dashboard">

<div class="cards">

<div class="card">
<i class="fas fa-cow"></i>
<h2><?php echo $animals; ?></h2>
<p>Total Animals</p>
<p class="analysis"><?php echo $animalPercent; ?>% of farm items</p>
</div>

<div class="card">
<i class="fas fa-seedling"></i>
<h2><?php echo $crops; ?></h2>
<p>Farm Crops</p>
<p class="analysis"><?php echo $cropPercent; ?>% of farm items</p>
</div>

<div class="card">
<i class="fas fa-users"></i>
<h2><?php echo $staff; ?></h2>
<p>Farm Staff</p>
<p class="analysis">Team ensuring smooth operations</p>
</div>

<div class="card">
<i class="fas fa-apple-alt"></i>
<h2><?php echo $produce; ?></h2>
<p>Produce Items</p>
<p class="analysis"><?php echo $producePercent; ?>% of farm items</p>
</div>

</div>

<div class="quick-links">
<a class="link" href="animals.php">
<i class="fas fa-cow"></i><br><br>
Manage Animals
</a>

<a class="link" href="crops.php">
<i class="fas fa-leaf"></i><br><br>
Manage Crops
</a>

<a class="link" href="staff.php">
<i class="fas fa-user"></i><br><br>
Manage Staff
</a>

<a class="link" href="produce.php">
<i class="fas fa-basket-shopping"></i><br><br>
Farm Produce
</a>

<a class="link" href="reports.php">
<i class="fas fa-chart-bar"></i><br><br>
Farm Reports
</a>
</div>

</div>

</body>
</html>