<?php
require_once 'auth.php';
require_once 'db.php';

// --- LOGIC: ADD CROP ---
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['crop_name']);
    $qty = (int)$_POST['quantity'];
    
    $img = 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=400';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $img = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $img);
    }

    $stmt = $conn->prepare("INSERT INTO crops (crop_name, quantity, image_path) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $name, $qty, $img);
    
    if ($stmt->execute()) {
        header("Location: crops.php?msg=added");
        exit();
    }
    $stmt->close();
}

// --- LOGIC: DELETE ---
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM crops WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    header("Location: crops.php?msg=deleted");
    exit();
}

$result = $conn->query("SELECT * FROM crops ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Inventory | Green Acres</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #27ae60; --dark: #1e272e; --light: #f8fafb;
            --white: #ffffff; --shadow: 0 10px 40px rgba(0,0,0,0.06);
            --warning: #f39c12; --danger: #eb4d4b;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; 
            display: flex; 
            background: var(--light); 
            color: #2d3436; 
            overflow-x: hidden;
        }

        /* Sidebar - Glassmorphism consistency */
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
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            border-left: 4px solid transparent; 
            font-weight: 500;
        }
        .sidebar a:hover, .sidebar a.active { 
            background: rgba(39, 174, 96, 0.1); 
            color: var(--white); 
            border-left-color: var(--primary); 
        }
        .sidebar a i { margin-right: 15px; width: 20px; font-size: 1.1rem; }

        /* Main Content with Entrance Animation */
        .main-content { 
            margin-left: 260px; 
            padding: 50px; 
            width: calc(100% - 260px); 
            animation: fadeInPage 0.8s ease-out;
        }

        @keyframes fadeInPage {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header-flex { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
        .header-flex h1 { margin: 0; font-size: 2.4rem; font-weight: 800; color: var(--dark); letter-spacing: -1px; }

        /* Action Card - Form Refinement */
        .action-card { 
            background: var(--white); 
            padding: 35px; 
            border-radius: 28px; 
            box-shadow: var(--shadow); 
            margin-bottom: 50px; 
            border: 1px solid rgba(0,0,0,0.02);
        }
        .form-grid { display: grid; grid-template-columns: 2fr 0.8fr 1.2fr auto; gap: 20px; align-items: flex-end; }
        .form-group label { display: block; font-size: 0.75rem; font-weight: 800; margin-bottom: 10px; color: #b2bec3; text-transform: uppercase; letter-spacing: 1px; }
        
        input { 
            width: 100%; 
            padding: 14px 18px; 
            border: 2px solid #f1f3f5; 
            border-radius: 16px; 
            background: #f8fafc; 
            font-family: inherit; 
            font-weight: 600;
            transition: 0.3s;
        }
        input:focus { outline: none; border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(39, 174, 96, 0.1); }
        
        .btn-add { 
            background: var(--primary); 
            color: white; 
            border: none; 
            padding: 15px 30px; 
            border-radius: 16px; 
            font-weight: 800; 
            cursor: pointer; 
            transition: 0.4s; 
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-add:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(39, 174, 96, 0.3); }

        /* Crop Grid & Cards */
        .crop-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; }
        .crop-card { 
            background: var(--white); 
            border-radius: 32px; 
            overflow: hidden; 
            box-shadow: var(--shadow); 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            position: relative; 
            border: 1px solid rgba(0,0,0,0.02);
        }
        .crop-card:hover { transform: translateY(-12px); box-shadow: 0 30px 60px rgba(0,0,0,0.12); }
        
        .crop-img-wrapper { height: 200px; overflow: hidden; position: relative; }
        .crop-img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s; }
        .crop-card:hover .crop-img { transform: scale(1.1); }

        .crop-info { padding: 30px; }
        .crop-info h3 { margin: 0; font-size: 1.4rem; font-weight: 800; color: var(--dark); }
        .crop-info p { color: #94a3b8; margin: 10px 0 20px; font-size: 0.95rem; display: flex; align-items: center; gap: 8px; font-weight: 500; }
        
        /* Status Badges - Professional Look */
        .badge { 
            position: absolute; top: 20px; left: 20px; z-index: 2;
            padding: 8px 16px; border-radius: 12px; font-size: 0.7rem; font-weight: 800; 
            text-transform: uppercase; letter-spacing: 0.5px; backdrop-filter: blur(8px);
        }
        .badge-success { background: rgba(39, 174, 96, 0.9); color: white; }
        .badge-warning { background: rgba(243, 156, 18, 0.9); color: white; }

        .delete-btn { 
            position: absolute; top: 20px; right: 20px; z-index: 2;
            background: rgba(255, 255, 255, 0.9); color: var(--danger); 
            width: 40px; height: 40px; border-radius: 12px; 
            display: flex; align-items: center; justify-content: center; 
            text-decoration: none; backdrop-filter: blur(10px); transition: 0.3s; 
        }
        .delete-btn:hover { background: var(--danger); color: white; transform: rotate(90deg); }

        .btn-group { display: flex; gap: 12px; margin-top: 10px; }
        .btn-outline { 
            flex: 1; padding: 12px; border-radius: 14px; border: 2px solid #f1f3f5; 
            background: white; font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .btn-dark { 
            flex: 1; padding: 12px; border-radius: 14px; border: none; 
            background: var(--dark); color: white; font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .btn-dark:hover { opacity: 0.85; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-leaf"></i> GREEN ACRES</div>
        <nav style="margin-top:20px;">
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="animals.php"><i class="fas fa-paw"></i> Livestock</a>
            <a href="birds.php"><i class="fas fa-dove"></i> Poultry</a>
            <a href="crops.php" class="active"><i class="fas fa-seedling"></i> Crops</a>
            <a href="produce.php"><i class="fas fa-warehouse"></i> Inventory</a>
            <a href="staff.php"><i class="fas fa-users-gear"></i> Team</a>
            <a href="logout.php" style="margin-top:100px; color:var(--danger);"><i class="fas fa-power-off"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header-flex">
            <div>
                <h1>Crop Inventory</h1>
                <p style="color:#a0a0a0; font-weight: 500;">Propagating excellence, one seed at a time.</p>
            </div>
            <div class="status-indicator">
                <span style="font-size: 0.75rem; font-weight: 800; color: var(--primary); background: #e6fff0; padding: 12px 24px; border-radius: 30px; border: 1px solid rgba(39, 174, 96, 0.2);">
                    <i class="fas fa-check-double"></i> HARVEST READY
                </span>
            </div>
        </div>

        <div class="action-card">
            <form method="POST" enctype="multipart/form-data" class="form-grid">
                <div class="form-group">
                    <label>Crop Variety</label>
                    <input type="text" name="crop_name" placeholder="e.g. Arabica Coffee" required>
                </div>
                <div class="form-group">
                    <label>Seedlings/Units</label>
                    <input type="number" name="quantity" placeholder="0" required>
                </div>
                <div class="form-group">
                    <label>Cover Image</label>
                    <input type="file" name="image" accept="image/*" style="border:none; background:transparent; padding:10px 0;">
                </div>
                <button type="submit" name="add" class="btn-add">
                    <i class="fas fa-plus"></i> Register Variety
                </button>
            </form>
        </div>

        <div class="crop-grid">
            <?php while($row = $result->fetch_assoc()): 
                $is_low = ($row['quantity'] < 10);
                $status_class = $is_low ? 'badge-warning' : 'badge-success';
                $status_text = $is_low ? 'Restock Required' : 'Optimal Growth';
            ?>
            <div class="crop-card">
                <a href="crops.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Archive this crop variety?')">
                    <i class="fas fa-trash-alt"></i>
                </a>
                
                <div class="crop-img-wrapper">
                    <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="crop-img" alt="Crop">
                </div>

                <div class="crop-info">
                    <h3><?php echo htmlspecialchars($row['crop_name']); ?></h3>
                    <p>
                        <i class="fas fa-cubes" style="color:var(--primary)"></i> 
                        <strong><?php echo number_format($row['quantity']); ?></strong> Units in Stock
                    </p>
                    
                    <div class="btn-group">
                         <button class="btn-outline"><i class="fas fa-pen-to-square"></i> Modify</button>
                         <button class="btn-dark"><i class="fas fa-clock-rotate-left"></i> Timeline</button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>