<?php
require_once 'auth.php';
require_once 'db.php';

$message = "";

// --- LOGIC: ADD ANIMAL ---
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['animal_name']);
    $qty = (int)$_POST['quantity'];
    
    $img = 'https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=400';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $img = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $img);
    }

    $stmt = $conn->prepare("INSERT INTO animals (animal_name, quantity, image_path) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $name, $qty, $img);
    
    if ($stmt->execute()) {
        header("Location: animals.php?msg=added");
        exit();
    }
    $stmt->close();
}

// --- LOGIC: DELETE ---
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM animals WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    header("Location: animals.php?msg=deleted");
    exit();
}

$result = $conn->query("SELECT * FROM animals ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livestock Registry | Green Acres</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #27ae60; --dark: #1e272e; --light: #f8fafb;
            --white: #ffffff; --shadow: 0 10px 40px rgba(0,0,0,0.06);
            --danger: #eb4d4b;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; 
            display: flex; 
            background-color: var(--light); 
            color: #2d3436; 
            overflow-x: hidden;
        }

        /* Sidebar - Enhanced Glassmorphism */
        .sidebar { 
            width: 260px; 
            background: var(--dark); 
            height: 100vh; 
            position: fixed; 
            color: #d1d8e0; 
            z-index: 1000;
        }
        .sidebar-brand { 
            padding: 40px 30px; 
            text-align: center; 
            font-size: 1.4rem; 
            font-weight: 800; 
            color: var(--primary); 
            letter-spacing: -1px;
        }
        .sidebar a { 
            display: flex; 
            align-items: center; 
            padding: 16px 28px; 
            color: #a5b1c2; 
            text-decoration: none; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
            font-weight: 500;
        }
        .sidebar a:hover, .sidebar a.active { 
            background: rgba(39, 174, 96, 0.1); 
            color: var(--white); 
            border-left-color: var(--primary); 
        }
        .sidebar i { margin-right: 15px; font-size: 1.1rem; }

        /* Main Content Animation */
        .main-content { 
            margin-left: 260px; 
            padding: 50px; 
            width: calc(100% - 260px); 
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modern Header */
        .header-box { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
        .header-box h1 { margin: 0; font-size: 2.4rem; font-weight: 800; color: var(--dark); letter-spacing: -1px; }

        /* Elegant Form Card */
        .card-form { 
            background: var(--white); 
            padding: 30px; 
            border-radius: 28px; 
            box-shadow: var(--shadow); 
            margin-bottom: 50px;
            border: 1px solid rgba(0,0,0,0.03);
        }
        .form-flex { display: grid; grid-template-columns: 1.5fr 0.8fr 1.2fr auto; gap: 20px; align-items: flex-end; }
        .form-group label { display: block; font-size: 0.75rem; font-weight: 700; color: #b2bec3; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
        
        input { 
            width: 100%; 
            padding: 14px 18px; 
            border: 2px solid #f1f3f5; 
            border-radius: 16px; 
            background: #f8fafc; 
            transition: 0.3s; 
            font-family: inherit;
            font-weight: 600;
        }
        input:focus { outline: none; border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(39, 174, 96, 0.1); }

        .btn-add { 
            background: var(--primary); 
            color: white; 
            border: none; 
            padding: 14px 30px; 
            border-radius: 16px; 
            font-weight: 700; 
            cursor: pointer; 
            transition: 0.4s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-add:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(39, 174, 96, 0.3); }

        /* Animal Cards - Professional Layout */
        .animal-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; }
        .animal-card { 
            background: var(--white); 
            border-radius: 32px; 
            overflow: hidden; 
            box-shadow: var(--shadow); 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            position: relative; 
            border: 1px solid rgba(0,0,0,0.02);
        }
        .animal-card:hover { transform: translateY(-12px); box-shadow: 0 30px 60px rgba(0,0,0,0.12); }

        .animal-img-wrapper { position: relative; height: 220px; overflow: hidden; }
        .animal-img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s; }
        .animal-card:hover .animal-img { scale: 1.1; }

        .animal-body { padding: 30px; }
        .animal-body h3 { margin: 0; font-size: 1.4rem; font-weight: 800; color: var(--dark); }
        
        .qty-badge { 
            background: #f0fff4; 
            color: var(--primary); 
            padding: 10px 18px; 
            border-radius: 14px; 
            font-weight: 800; 
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* Floating Delete Action */
        .btn-del { 
            position: absolute; top: 20px; right: 20px;
            width: 40px; height: 40px; border-radius: 12px; 
            display: flex; align-items: center; justify-content: center; 
            background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);
            color: var(--danger); transition: 0.3s; text-decoration: none;
        }
        .btn-del:hover { background: var(--danger); color: white; transform: rotate(90deg); }

        /* Notification Toast */
        .toast {
            position: fixed; top: 30px; right: 30px; background: var(--dark); color: white;
            padding: 15px 25px; border-radius: 15px; font-weight: 600; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 9999; animation: toastSlide 0.5s ease-out;
        }
        @keyframes toastSlide { from { transform: translateX(100%); } to { transform: translateX(0); } }
    </style>
</head>
<body>

    <?php if(isset($_GET['msg'])): ?>
        <div class="toast" id="toast">
            <i class="fas fa-check-circle" style="color:var(--primary)"></i> 
            Record <?php echo $_GET['msg'] == 'added' ? 'Created' : 'Updated'; ?> Successfully
        </div>
        <script>setTimeout(() => document.getElementById('toast').style.display='none', 3000);</script>
    <?php endif; ?>

    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-leaf"></i> GREEN ACRES</div>
        <nav style="margin-top:20px;">
            <a href="dashboard.php"><i class="fas fa-grid-2"></i> Dashboard</a>
            <a href="animals.php" class="active"><i class="fas fa-paw"></i> Livestock</a>
            <a href="birds.php"><i class="fas fa-dove"></i> Poultry</a>
            <a href="crops.php"><i class="fas fa-seedling"></i> Crop Cycles</a>
            <a href="produce.php"><i class="fas fa-warehouse"></i> Inventory</a>
            <a href="staff.php"><i class="fas fa-users-gear"></i> Team</a>
            <a href="logout.php" style="margin-top:100px; color:var(--danger);"><i class="fas fa-power-off"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header-box">
            <div>
                <h1>Livestock Hub</h1>
                <p style="color: #a0a0a0; font-weight: 500;">Monitor and scale your animal population.</p>
            </div>
            <div class="status-indicator">
                <span style="font-size: 0.75rem; font-weight: 800; color: var(--primary); background: #e6fff0; padding: 10px 20px; border-radius: 30px; border: 1px solid rgba(39, 174, 96, 0.2);">
                    <i class="fas fa-circle-check"></i> DATABASE SYNCED
                </span>
            </div>
        </div>

        <div class="card-form">
            <form method="POST" enctype="multipart/form-data" class="form-flex">
                <div class="form-group">
                    <label>Animal / Breed</label>
                    <input type="text" name="animal_name" placeholder="e.g. Boer Goat" required>
                </div>
                <div class="form-group">
                    <label>Head Count</label>
                    <input type="number" name="quantity" placeholder="0" required>
                </div>
                <div class="form-group">
                    <label>Reference Image</label>
                    <input type="file" name="image" accept="image/*" style="border:none; background:transparent; padding:10px 0;">
                </div>
                <button type="submit" name="add" class="btn-add">
                    <i class="fas fa-plus"></i> Register Animal
                </button>
            </form>
        </div>

        <div class="animal-grid">
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="animal-card">
                <a href="animals.php?delete=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('Archive this livestock record?')">
                    <i class="fas fa-times"></i>
                </a>
                
                <div class="animal-img-wrapper">
                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="animal-img" alt="Livestock">
                </div>
                
                <div class="animal-body">
                    <h3><?php echo htmlspecialchars($row['animal_name']); ?></h3>
                    <p style="color:#b2bec3; font-size: 0.8rem; margin: 8px 0 20px 0; font-weight: 600;">
                        <i class="fas fa-fingerprint"></i> UNIT_REF: #GA-<?php echo $row['id'] + 1000; ?>
                    </p>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="qty-badge">
                            <i class="fas fa-arrow-up-right-dots"></i> <?php echo $row['quantity']; ?> Heads
                        </span>
                        <div style="font-size: 0.8rem; color: var(--primary); font-weight: 800; display: flex; align-items: center; gap: 5px;">
                            <i class="fas fa-shield-heart"></i> STABLE
                        </div>
                    </div>
                    
                    <div style="margin-top: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <button style="padding: 12px; border-radius: 14px; border: 2px solid #f1f3f5; background: #fff; cursor: pointer; font-size: 0.8rem; font-weight: 700; transition: 0.3s;" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='#f1f3f5'">
                            Health Log
                        </button>
                        <button style="padding: 12px; border-radius: 14px; border: none; background: var(--dark); color: white; cursor: pointer; font-size: 0.8rem; font-weight: 700; transition: 0.3s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                            Analytics
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>