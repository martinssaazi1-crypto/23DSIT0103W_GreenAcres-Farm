<?php
session_start();
require_once 'db.php';

/* AUTHENTICATION CHECK */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// Fetch real-time counts for the dashboard cards
$animal_count = $conn->query("SELECT SUM(quantity) as total FROM animals")->fetch_assoc()['total'] ?? 0;
$produce_count = $conn->query("SELECT COUNT(*) as total FROM produce")->fetch_assoc()['total'] ?? 0;
$staff_count = $conn->query("SELECT COUNT(*) as total FROM staff")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Green Acres | Farm Profile</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #27ae60;
            --dark: #1e272e;
            --accent: #f39c12;
            --light: #f4f7f6;
            --white: #ffffff;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            background-color: var(--light);
            color: var(--dark);
            scroll-behavior: smooth;
        }

        /* NAVIGATION OVERLAY */
        header {
            background: var(--dark);
            color: white;
            padding: 20px 5%;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .logo-brand { font-size: 1.5rem; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px; }

        nav ul { display: flex; list-style: none; gap: 25px; margin: 0; }
        nav ul li a { color: #a5b1c2; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: 0.3s; }
        nav ul li a:hover { color: var(--primary); }

        .btn-dash { background: var(--primary); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 0.8rem; }

        /* HERO SECTION */
        .hero {
            height: 60vh;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 0 20px;
        }

        .hero h1 { font-size: 3.5rem; margin: 0; letter-spacing: -2px; }
        .hero p { font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin-top: 15px; }

        /* QUICK STATS CARDS (Linked to main pages) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: -60px auto 50px;
            padding: 0 20px;
        }

        .stat-card {
            background: var(--white);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            transition: 0.3s;
            text-decoration: none;
            color: inherit;
        }

        .stat-card:hover { transform: translateY(-10px); }
        .stat-card i { font-size: 2.5rem; color: var(--primary); margin-bottom: 15px; }
        .stat-card h3 { margin: 10px 0; font-size: 0.9rem; text-transform: uppercase; color: #7f8c8d; letter-spacing: 1px; }
        .stat-card .value { font-size: 2rem; font-weight: 800; color: var(--dark); }

        /* CONTENT SECTIONS */
        section { max-width: 1000px; margin: 80px auto; padding: 0 20px; }
        .section-title { font-size: 2rem; margin-bottom: 30px; position: relative; padding-bottom: 10px; }
        .section-title::after { content: ''; position: absolute; left: 0; bottom: 0; width: 60px; height: 5px; background: var(--primary); border-radius: 10px; }

        .about-flex { display: flex; gap: 40px; align-items: center; }
        .about-text { flex: 1; line-height: 1.8; font-size: 1.1rem; color: #4b6584; }
        .about-img { flex: 1; border-radius: 25px; box-shadow: 20px 20px 0 var(--primary); }

        /* PRODUCE GRID */
        .produce-list { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .produce-item { background: white; padding: 20px; border-radius: 15px; display: flex; align-items: center; gap: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .produce-item i { color: var(--primary); font-size: 1.5rem; }

        footer { background: var(--dark); color: white; padding: 60px 5%; text-align: center; }
        .socials { margin: 25px 0; }
        .socials a { color: white; font-size: 1.5rem; margin: 0 15px; transition: 0.3s; }
        .socials a:hover { color: var(--primary); }
    </style>
</head>

<body>

    <header>
        <div class="logo-brand">
            <i class="fas fa-leaf"></i> GREEN ACRES
        </div>
        <nav>
            <ul>
                <li><a href="#about">About</a></li>
                <li><a href="#produce">Produce</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
        <a href="suppliers.php"><i class="fas fa-truck-ramp-box"></i> Suppliers</a>
        <a href="dashboard.php" class="btn-dash">GO TO DASHBOARD <i class="fas fa-arrow-right"></i></a>
    </header>

    <div class="hero">
        <h1>Nurturing Nature</h1>
        <p>Sustainable farming practices delivering organic excellence from our fields to your table.</p>
    </div>

    <div class="stats-grid">
        <a href="animals.php" class="stat-card">
            <i class="fas fa-cow"></i>
            <h3>Livestock</h3>
            <div class="value"><?php echo $animal_count; ?> Heads</div>
        </a>
        <a href="produce.php" class="stat-card">
            <i class="fas fa-basket-shopping"></i>
            <h3>Inventory</h3>
            <div class="value"><?php echo $produce_count; ?> Items</div>
        </a>
        <a href="staff.php" class="stat-card">
            <i class="fas fa-users-gear"></i>
            <h3>Farm Staff</h3>
            <div class="value"><?php echo $staff_count; ?> Experts</div>
        </a>
    </div>

    <section id="about">
        <h2 class="section-title">Rooted in Excellence</h2>
        <div class="about-flex">
            <div class="about-text">
                <p>Founded by <strong>Martin Ssaazi</strong>, Green Acres was born from a vision of sustainable agriculture in the heart of Wakiso District. We believe that farming should give back to the Earth as much as it takes.</p>
                <p>Our 50-acre facility utilizes advanced irrigation and organic fertilization to ensure that every tomato, mango, and herb is packed with nutrients and free from harmful chemicals.</p>
            </div>
            <img src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=600&q=80" class="about-img" alt="Farm Field">
        </div>
    </section>

    <section id="produce" style="background: white; padding: 60px 40px; border-radius: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.05);">
        <h2 class="section-title">Our Premium Produce</h2>
        <div class="produce-list">
            <div class="produce-item"><i class="fas fa-check-circle"></i> Organic Vegetables (Spinach, Carrots)</div>
            <div class="produce-item"><i class="fas fa-check-circle"></i> Exotic Fruits (Mangoes, Bananas)</div>
            <div class="produce-item"><i class="fas fa-check-circle"></i> Culinary Herbs (Basil, Mint)</div>
            <div class="produce-item"><i class="fas fa-check-circle"></i> Dairy & Poultry (Fresh Milk, Eggs)</div>
        </div>
        
        <h2 class="section-title" style="margin-top: 60px;">Specialized Services</h2>
        <div class="produce-list">
            <div class="produce-item"><i class="fas fa-bus"></i> Educational Farm Tours</div>
            <div class="produce-item"><i class="fas fa-truck-fast"></i> Organic Doorstep Delivery</div>
            <div class="produce-item"><i class="fas fa-graduation-cap"></i> Sustainable Farming Workshops</div>
        </div>
    </section>

    <section id="contact" style="text-align: center;">
        <h2 class="section-title" style="display:inline-block">Connect With Us</h2>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 40px;">
            <div class="stat-card">
                <i class="fas fa-envelope-open-text"></i>
                <p>info@greenacresfarm.com</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-phone-volume"></i>
                <p>+256 700 000 000</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-location-dot"></i>
                <p>Wakiso District, Uganda</p>
            </div>
        </div>
    </section>

    <footer>
        <div class="logo-brand" style="justify-content: center; color: white; margin-bottom: 20px;">
            <i class="fas fa-leaf"></i> GREEN ACRES
        </div>
        <p>Leading the way in Sustainable Agriculture in East Africa.</p>
        <div class="socials">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin"></i></a>
            <a href="#"><i class="fab fa-whatsapp"></i></a>
        </div>
        <p style="opacity: 0.5; font-size: 0.8rem;">&copy; 2026 Green Acres Farm Management System. All rights reserved.</p>
    </footer>

</body>
</html>