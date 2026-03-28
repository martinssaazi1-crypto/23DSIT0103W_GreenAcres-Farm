<?php
require_once 'auth.php';
require_once 'db.php';

// --- LOGIC: ADD PRODUCE ---
if(isset($_POST['add'])){
    $name = mysqli_real_escape_string($conn, $_POST['produce_name']);
    $qty = (int)$_POST['quantity'];
    
    $img = 'https://images.unsplash.com/photo-1606787366850-de6330128bfc?w=400';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $img = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $img);
    }
    
    $stmt = $conn->prepare("INSERT INTO produce (produce_name, quantity, image_path) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $name, $qty, $img);
    $stmt->execute();
    $stmt->close();
}

// --- LOGIC: QUICK STOCK ADJUST ---
if(isset($_GET['adjust']) && isset($_GET['id'])){
    $id = (int)$_GET['id'];
    $amt = (int)$_GET['adjust'];
    $conn->query("UPDATE produce SET quantity = GREATEST(0, quantity + $amt) WHERE id = $id");
    header("Location: produce.php?msg=updated");
    exit();
}

// --- LOGIC: DELETE ---
if(isset($_GET['delete'])){
    $stmt = $conn->prepare("DELETE FROM produce WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    header("Location: produce.php?msg=deleted");
    exit();
}

$result = $conn->query("SELECT * FROM produce ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Hub | Green Acres</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #27ae60; --dark: #1e272e; --light: #f8fafb;
            --white: #ffffff; --shadow: 0 10px 40px rgba(0,0,0,0.06);
            --accent: #3498db; --danger: #eb4d4b; --warning: #f39c12;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; 
            display: flex; 
            background: var(--light); 
            color: #2d3436; 
            overflow-x: hidden;
        }

        /* Sidebar - Uniform System Style */
        .sidebar { width: 260px; background: var(--dark); height: 100vh; position: fixed; color: #d1d8e0; z-index: 1000; }
        .sidebar-brand { padding: 40px 30px; text-align: center; font-size: 1.4rem; font-weight: 800; color: var(--primary); letter-spacing: -1px; }
        .sidebar a { 
            display: flex; align-items: center; padding: 16px 28px; color: #a5b1c2; text-decoration: none; 
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 4px solid transparent; font-weight: 500;
        }
        .sidebar a:hover, .sidebar a.active { background: rgba(39, 174, 96, 0.1); color: var(--white); border-left-color: var(--primary); }
        .sidebar a i { margin-right: 15px; width: 20px; font-size: 1.1rem; }

        /* Main Area with Animation */
        .main-content { 
            margin-left: 260px; padding: 50px; width: calc(100% - 260px); 
            animation: fadeInPage 0.8s ease-out;
        }
        @keyframes fadeInPage { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

        .page-header { margin-bottom: 40px; }
        .page-header h1 { margin: 0; font-size: 2.4rem; font-weight: 800; color: var(--dark); letter-spacing: -1px; }

        /* Card Form Refinement */
        .card { 
            background: var(--white); padding: 35px; border-radius: 28px; 
            box-shadow: var(--shadow); margin-bottom: 40px; border: 1px solid rgba(0,0,0,0.02);
        }
        .add-form { display: grid; grid-template-columns: 2fr 0.8fr 1.2fr auto; gap: 20px; align-items: flex-end; }
        .form-group label { display: block; font-size: 0.75rem; font-weight: 800; margin-bottom: 10px; color: #b2bec3; text-transform: uppercase; letter-spacing: 1px; }
        
        input { 
            width: 100%; padding: 14px 18px; border: 2px solid #f1f3f5; border-radius: 16px; 
            background: #f8fafc; font-family: inherit; font-weight: 600; transition: 0.3s;
        }
        input:focus { outline: none; border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(39, 174, 96, 0.1); }
        
        .btn-add { 
            background: var(--primary); color: white; border: none; padding: 15px 30px; 
            border-radius: 16px; font-weight: 800; cursor: pointer; transition: 0.4s;
        }
        .btn-add:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(39, 174, 96, 0.3); }

        /* Table Design */
        .table-container { background: white; border-radius: 32px; overflow: hidden; box-shadow: var(--shadow); border: 1px solid rgba(0,0,0,0.03); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #fcfdfe; padding: 22px 25px; text-align: left; font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; }
        td { padding: 20px 25px; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
        tr:last-child td { border: none; }
        
        .img-cell { width: 60px; height: 60px; object-fit: cover; border-radius: 18px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        
        .stock-badge { 
            padding: 8px 16px; border-radius: 12px; font-size: 0.85rem; font-weight: 800; 
            display: inline-flex; align-items: center; gap: 8px;
        }
        .instock { background: #e6fff0; color: #27ae60; }
        .lowstock { background: #fff4e6; color: #f39c12; }

        /* Action Buttons - Snap Effects */
        .adjust-group { display: flex; gap: 8px; align-items: center; }
        .adjust-btn { 
            text-decoration: none; padding: 10px 14px; border-radius: 12px; 
            font-size: 0.75rem; font-weight: 800; transition: 0.2s; 
            border: 2px solid #f1f3f5; color: var(--dark); background: #fff;
        }
        .adjust-btn:hover { border-color: var(--primary); color: var(--primary); transform: translateY(-2px); }
        
        .sell-btn { background: var(--dark); color: white; border: none; padding: 10px 20px; }
        .sell-btn:hover { background: var(--accent); color: white; border-color: var(--accent); }
        
        .delete-link { 
            color: #cbd5e1; transition: 0.3s; font-size: 1.1rem; padding: 10px;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .delete-link:hover { color: var(--danger); transform: scale(1.2); }

        .sku-label { background: #f1f5f9; color: #64748b; padding: 2px 8px; border-radius: 6px; font-family: monospace; font-size: 0.75rem; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-leaf"></i> GREEN ACRES</div>
        <nav style="margin-top:20px;">
            <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="animals.php"><i class="fas fa-paw"></i> Livestock</a>
            <a href="crops.php"><i class="fas fa-leaf"></i> Crops</a>
            <a href="produce.php" class="active"><i class="fas fa-boxes-stacked"></i> Produce</a>
            <a href="suppliers.php"><i class="fas fa-truck-fast"></i> Suppliers</a>
            <a href="staff.php"><i class="fas fa-users-gear"></i> Team</a>
            <a href="logout.php" style="color: var(--danger); margin-top: 50px;"><i class="fas fa-power-off"></i> Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Inventory Hub</h1>
            <p style="color: #94a3b8; font-weight: 500;">Real-time warehouse tracking & dispatch control.</p>
        </header>

        <section class="card">
            <form method="POST" enctype="multipart/form-data" class="add-form">
                <div class="form-group">
                    <label>Produce Identity</label>
                    <input type="text" name="produce_name" placeholder="e.g. Organic Honey (500ml)" required>
                </div>
                <div class="form-group">
                    <label>Initial Quantity</label>
                    <input type="number" name="quantity" placeholder="0" required>
                </div>
                <div class="form-group">
                    <label>Product Visual</label>
                    <input type="file" name="image" accept="image/*" style="border:none; background:transparent; padding:10px 0;">
                </div>
                <button name="add" class="btn-add">
                    <i class="fas fa-plus-circle" style="margin-right:8px;"></i> Register Stock
                </button>
            </form>
        </section>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Visual</th>
                        <th>Item Details</th>
                        <th>Stock Status</th>
                        <th>Quick Adjustments</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): 
                        $is_low = ($row['quantity'] <= 20);
                        $statusClass = $is_low ? 'lowstock' : 'instock';
                        $statusText = $is_low ? 'Low Stock' : 'In Stock';
                    ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="img-cell"></td>
                        <td>
                            <strong style="font-size: 1.1rem; color: var(--dark); display:block; margin-bottom:4px;"><?php echo htmlspecialchars($row['produce_name']); ?></strong>
                            <span class="sku-label">SKU: GA-PRD-<?php echo 1000 + $row['id']; ?></span>
                        </td>
                        <td>
                            <span class="stock-badge <?php echo $statusClass; ?>">
                                <i class="fas <?php echo $is_low ? 'fa-triangle-exclamation' : 'fa-circle-check'; ?>"></i>
                                <?php echo $row['quantity']; ?> Units
                            </span>
                        </td>
                        <td>
                            <div class="adjust-group">
                                <a href="produce.php?id=<?php echo $row['id']; ?>&adjust=5" class="adjust-btn">+5</a>
                                <a href="produce.php?id=<?php echo $row['id']; ?>&adjust=-1" class="adjust-btn" style="color: var(--warning);">-1</a>
                                <a href="produce.php?id=<?php echo $row['id']; ?>&adjust=-10" class="adjust-btn sell-btn">Dispatch 10</a>
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <a href="produce.php?delete=<?php echo $row['id']; ?>" class="delete-link" onclick="return confirm('Remove item from inventory permanently?')">
                                <i class="fas fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>