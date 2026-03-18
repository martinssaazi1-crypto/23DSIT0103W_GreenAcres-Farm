<?php
session_start();

/* AUTHENTICATION CHECK */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>GREEN ACRES FARMS</title>

<link rel="stylesheet" href="Assets/CSS/style.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

/* BODY */
body{
    font-family:Arial, sans-serif;
    margin:0;
    color:#2c3e50;

    background:
    linear-gradient(rgba(255,255,255,0.85), rgba(255,255,255,0.85)),
    url("https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=1350&q=80");

    background-size:cover;
    background-position:center;
}

/* HEADER */
header, footer{
    background-color:rgba(102,205,170,0.95);
    color:white;
    padding:15px 20px;
}

.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

/* LOGO */
.profile img{
    width:95px;
    height:95px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid white;
    box-shadow:0 6px 15px rgba(0,0,0,0.3);
}

/* NAVIGATION */
nav ul{
    display:flex;
    list-style:none;
    padding:0;
}

nav ul li{
    margin-right:20px;
}

nav ul li a{
    color:white;
    text-decoration:none;
    font-weight:bold;
    padding:6px 10px;
    border-radius:5px;
}

nav ul li a:hover{
    background:white;
    color:#3cb371;
}

/* MAIN */
main{
    padding:25px;
}

/* HERO SECTION */
.hero{
    background:
    linear-gradient(rgba(0,0,0,0.5),rgba(0,0,0,0.5)),
    url("https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1350&q=80");

    background-size:cover;
    background-position:center;

    color:white;
    padding:50px;
    border-radius:12px;
    margin-bottom:25px;
    text-align:center;
}

.hero h2{
    font-size:32px;
    margin-bottom:10px;
}

.hero p{
    font-size:16px;
}

/* DASHBOARD CARDS */
.dashboard-cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:20px;
    margin-bottom:25px;
}

.card{
    background:white;
    padding:15px;
    border-radius:12px;
    text-align:center;
    box-shadow:0 6px 15px rgba(0,0,0,0.2);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-4px);
}

.card i{
    font-size:28px;
    color:#FF8C00;
    margin-bottom:8px;
}

.card h3{
    margin:8px 0 5px 0;
    font-size:18px;
    color:#FF8C00;
}

.card p{
    margin:0;
    font-size:16px;
    font-weight:bold;
}

/* SECTIONS */
section{
    background:rgba(255,255,255,0.9);
    margin:20px 0;
    padding:20px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,0.2);
    transition:0.3s;
}

section:hover{
    transform:translateY(-3px);
}

section h2{
    border-bottom:3px solid #FF8C00;
    padding-bottom:5px;
}

/* FOOTER */
footer{
    text-align:center;
    padding:18px;
}

.socials a{
    color:white;
    margin:0 10px;
    font-size:22px;
}

/* BACK BUTTON */
.back-dashboard{
    position:fixed;
    bottom:20px;
    right:20px;
    background:#FF8C00;
    color:white;
    padding:12px 20px;
    border-radius:8px;
    font-weight:bold;
    text-decoration:none;
    box-shadow:0 4px 10px rgba(0,0,0,0.3);
}

.back-dashboard:hover{
    background:#e67300;
    transform:scale(1.05);
}

.logout-btn{
    color:white;
    font-weight:bold;
    text-decoration:none;
}

</style>
</head>

<body>

<a href="dashboard.php" class="back-dashboard">
← Back to Dashboard
</a>

<header>

<div class="top-bar">

<div class="logo-section">
<div class="profile">
<img src="assets/images/logo.jpg" alt="Green Acres Logo">
</div>
</div>

<div class="welcome-section">
<h1>GREEN ACRES FARM</h1>
<p>
Welcome,! Your farm management gives you quick insights into animals, crops, staff, and sales to keep your farm running smoothly.
</p>
</div>

<a href="logout.php" class="logout-btn">Logout</a>

</div>

<nav>
<ul>
<li><a href="#about">About</a></li>
<li><a href="#founder">Founder</a></li>
<li><a href="#produce">Our Produce</a></li>
<li><a href="#services">Services</a></li>
<li><a href="#staff">Farm Staff</a></li>
<li><a href="#contact">Contact</a></li>
</ul>
</nav>

</header>

<main>

<!-- HERO -->


<!-- DASHBOARD CARDS -->
<div class="dashboard-cards">

<div class="card">
<i class="fa-solid fa-cow"></i>
<h3>Farm Animals</h3>
<p>8</p>
</div>

<div class="card">
<i class="fa-solid fa-carrot"></i>
<h3>Produce</h3>
<p>4 Types</p>
</div>

<div class="card">
<i class="fa-solid fa-users"></i>
<h3>Farm Staff</h3>
<p>6</p>
</div>

<div class="card">
<i class="fa-solid fa-chart-line"></i>
<h3>Farm Sales</h3>
<p>$4,500</p>
</div>

</div>

<section id="about">
<h2>About Green Acres</h2>
<p>
Green Acres is a sustainable farm dedicated to producing fresh, organic vegetables, fruits, and herbs.
We focus on environmentally friendly practices that benefit both nature and the community.
</p>
</section>

<section id="founder">
<h2>Founder</h2>
<p>
Green Acres Farm was founded by <strong>Martin Ssaazi</strong>, an agricultural enthusiast passionate about sustainable farming and community development.
</p>
</section>

<section id="produce">
<h2>Our Produce</h2>

<ul>
<li>Fresh Vegetables: Tomatoes, Spinach, Carrots</li>
<li>Fruits: Mangoes, Bananas, Strawberries</li>
<li>Herbs: Basil, Mint, Coriander</li>
<li>Dairy Products: Fresh Milk from cows</li>
<li>Farm Eggs from free-range chickens</li>
</ul>

</section>

<section id="services">
<h2>Services</h2>

<ul>
<li>Farm Tours and Educational Visits</li>
<li>Organic Produce Delivery</li>
<li>Workshops on Sustainable Farming</li>
<li>Agricultural Training for Students</li>
</ul>

</section>

<section id="staff">
<h2>Our Farm Staff</h2>

<p>
Green Acres Farm is supported by a hardworking team of agricultural professionals and farm workers.
</p>

</section>

<section id="contact">
<h2>Contact Us</h2>

<p>Email: info@greenacresfarm.com</p>
<p>Phone: +256 700 000 000</p>
<p>Location: Wakiso District, Uganda</p>

</section>

</main>

<footer>

<p>Follow Us</p>

<div class="socials">
<a href="#"><i class="fab fa-tiktok"></i></a>
<a href="#"><i class="fab fa-x"></i></a>
</div>

<p>© 2026 Green Acres Farm</p>

</footer>

</body>
</html>