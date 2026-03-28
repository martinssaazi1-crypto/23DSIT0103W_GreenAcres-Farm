<?php
require_once 'auth.php';
require_once 'db.php';

// --- LOGIC: ADD STAFF WITH PHOTO ---
if (isset($_POST['add_staff'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $salary = floatval($_POST['salary']);
    
    $photo_name = null;
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
        $photo_name = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "", $name) . "." . $file_ext;
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $photo_name);
    }

    $stmt = $conn->prepare("INSERT INTO staff (name, role, phone, salary, status, photo) VALUES (?, ?, ?, ?, 'Active', ?)");
    $stmt->bind_param("sssds", $name, $role, $phone, $salary, $photo_name);
    $stmt->execute();
    header("Location: staff.php?status=added");
    exit();
}

// --- LOGIC: TOGGLE STATUS ---
if (isset($_GET['toggle_id'])) {
    $id = intval($_GET['toggle_id']);
    $current = $_GET['current'] ?? 'Active';
    $new_status = ($current == 'Active') ? 'Off-duty' : 'Active';
    $conn->query("UPDATE staff SET status = '$new_status' WHERE id = $id");
    header("Location: staff.php");
    exit();
}

// --- LOGIC: DELETE STAFF ---
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $res = $conn->query("SELECT photo FROM staff WHERE id = $id");
    $row = $res->fetch_assoc();
    if (!empty($row['photo']) && file_exists("uploads/" . $row['photo'])) {
        unlink("uploads/" . $row['photo']);
    }
    $conn->query("DELETE FROM staff WHERE id = $id");
    header("Location: staff.php?status=deleted");
    exit();
}

// --- DATA FETCHING ---
$staff_members = $conn->query("SELECT * FROM staff ORDER BY name ASC");
$stats = $conn->query("SELECT COUNT(*) as total_count, SUM(salary) as total_payroll FROM staff WHERE status = 'Active'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Human Resources | Green Acres</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { 
            --primary: #27ae60; 
            --dark: #1e272e; 
            --light: #f8fafb; 
            --white: #ffffff; 
            --accent: #3498db;
            --danger: #ff7675;
            --shadow: 0 10px 40px rgba(0,0,0,0.06); 
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; 
            display: flex; 
            background: var(--light); 
            color: #2d3436;
        }
        
        /* Sidebar Refined */
        .sidebar { width: 260px; background: var(--dark); height: 100vh; position: fixed; color: #d1d8e0; z-index: 100;}
        .sidebar-brand { padding: 40px 30px; text-align: center; font-size: 1.4rem; font-weight: 800; color: var(--primary); letter-spacing: -1px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar a { display: flex; align-items: center; padding: 16px 28px; color: #a5b1c2; text-decoration: none; border-left: 4px solid transparent; transition: 0.3s; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background: rgba(39, 174, 96, 0.1); color: var(--white); border-left-color: var(--primary); }
        .sidebar i { margin-right: 15px; width: 20px; font-size: 1.1rem; }

        /* Main Layout */
        .main-content { margin-left: 260px; padding: 50px; width: calc(100% - 260px); animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

        /* Stats Bar */
        .stats-bar { display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px; margin-bottom: 40px; }
        .stat-item { 
            background: white; padding: 25px; border-radius: 24px; display: flex; align-items: center; gap: 20px; 
            box-shadow: var(--shadow); transition: 0.3s; border: 1px solid rgba(0,0,0,0.02);
        }
        .stat-item:hover { transform: translateY(-5px); }
        .stat-icon { 
            width: 60px; height: 60px; background: #e8f5e9; color: var(--primary); 
            border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; 
        }

        /* Form Card */
        .card { 
            background: var(--white); padding: 35px; border-radius: 28px; 
            box-shadow: var(--shadow); margin-bottom: 40px; border: 1px solid rgba(0,0,0,0.02);
        }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: flex-end; }
        .form-grid label { display: block; font-size: 0.75rem; font-weight: 800; margin-bottom: 10px; color: #b2bec3; text-transform: uppercase; letter-spacing: 1px; }
        input, select { 
            width: 100%; padding: 14px 18px; border: 2px solid #f1f3f5; border-radius: 16px; 
            background: #f8fafc; font-family: inherit; font-weight: 600; transition: 0.3s;
        }
        input:focus, select:focus { outline: none; border-color: var(--primary); background: #fff; }

        /* Search Bar Glassmorphism */
        .search-container { position: relative; width: 350px; }
        .search-container i { position: absolute; left: 20px; top: 16px; color: #b2bec3; z-index: 2; }
        .search-bar { 
            padding: 14px 20px 14px 55px; border-radius: 50px; border: 2px solid #fff; 
            background: #fff; width: 100%; box-shadow: var(--shadow); font-weight: 600;
        }

        /* Staff Cards */
        .staff-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; }
        .staff-card { 
            background: white; padding: 40px 30px 30px; border-radius: 32px; text-align: center; 
            box-shadow: var(--shadow); transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            position: relative; border: 1px solid rgba(0,0,0,0.03);
            overflow: hidden;
        }
        .staff-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(0,0,0,0.1); }
        
        .avatar { 
            width: 120px; height: 120px; background: #f1f3f5; border-radius: 40px; 
            margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; 
            font-size: 3rem; color: #cbd5e1; overflow: hidden; border: 5px solid #fff; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.08); transition: 0.4s;
        }
        .staff-card:hover .avatar { transform: scale(1.05) rotate(3deg); }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        
        .status-badge { 
            font-size: 0.65rem; padding: 6px 14px; border-radius: 12px; font-weight: 800; 
            text-transform: uppercase; letter-spacing: 1px; display: inline-block; margin-bottom: 10px;
        }
        .status-active { background: #e6fff0; color: #27ae60; }
        .status-off { background: #f1f3f5; color: #94a3b8; }

        /* Action Buttons */
        .btn-call { 
            display: inline-flex; align-items: center; justify-content: center; 
            width: 45px; height: 45px; border-radius: 15px; background: var(--primary); 
            color: white; text-decoration: none; transition: 0.3s; font-size: 1.2rem;
        }
        .btn-call:hover { transform: translateY(-3px) scale(1.1); box-shadow: 0 10px 20px rgba(39, 174, 96, 0.2); }
        
        .delete-btn { position: absolute; top: 25px; right: 25px; color: #cbd5e1; transition: 0.3s; font-size: 1.1rem; }
        .delete-btn:hover { color: var(--danger); transform: rotate(90deg); }

        .edit-btn { position: absolute; top: 25px; left: 25px; color: #cbd5e1; transition: 0.3s; font-size: 1.1rem; }
        .edit-btn:hover { color: var(--accent); transform: scale(1.2); }

        .payroll-box {
            background: #f8fafc; border-radius: 20px; padding: 15px; 
            display: flex; justify-content: space-between; align-items: center; margin-top: 25px;
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-brand">GREEN ACRES</div>
        <nav style="margin-top:20px;">
            <a href="dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="animals.php"><i class="fas fa-paw"></i> Livestock</a>
            <a href="crops.php"><i class="fas fa-seedling"></i> Crops</a>
            <a href="produce.php"><i class="fas fa-boxes-stacked"></i> Produce</a>
            <a href="suppliers.php"><i class="fas fa-truck-fast"></i> Suppliers</a>
            <a href="staff.php" class="active"><i class="fas fa-user-group"></i> Staff</a>
            <a href="logout.php" style="margin-top:50px; color:var(--danger);"><i class="fas fa-power-off"></i> Logout</a>
        </nav>
    </aside>

    <div class="main-content">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <div>
                <h1 style="margin:0; font-size: 2.4rem; font-weight: 800; letter-spacing: -1px;">Farm Team</h1>
                <p style="color: #94a3b8; font-weight: 500;">Manage workforce and payroll operations.</p>
            </div>
            <div class="search-container">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" id="staffSearch" onkeyup="filterStaff()" class="search-bar" placeholder="Search by name...">
            </div>
        </header>

        <section class="stats-bar">
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                <div>
                    <h4 style="margin:0; color:#94a3b8; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Active Personnel</h4>
                    <p style="margin:0; font-size:1.8rem; font-weight:800;"><?php echo $stats['total_count'] ?? 0; ?></p>
                </div>
            </div>
            <div class="stat-item" style="border-right: 4px solid var(--accent);">
                <div class="stat-icon" style="background:#e3f2fd; color:var(--accent);"><i class="fas fa-wallet"></i></div>
                <div>
                    <h4 style="margin:0; color:#94a3b8; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Monthly Liability</h4>
                    <p style="margin:0; font-size:1.8rem; font-weight:800;"><?php echo number_format($stats['total_payroll'] ?? 0); ?> <small style="font-size: 0.9rem;">UGX</small></p>
                </div>
            </div>
        </section>
        
        <section class="card">
            <h3 style="margin-top:0; font-weight: 800; margin-bottom: 25px;"><i class="fas fa-user-plus" style="color:var(--primary); margin-right: 10px;"></i> Onboard New Member</h3>
            <form method="POST" enctype="multipart/form-data" class="form-grid">
                <div><label>Full Name</label><input type="text" name="name" placeholder="John Doe" required></div>
                <div><label>Assign Role</label>
                    <select name="role">
                        <option>Farm Manager</option><option>Vet Technician</option>
                        <option>Field Hand</option><option>Security</option><option>Driver</option>
                    </select>
                </div>
                <div><label>Phone Number</label><input type="text" name="phone" placeholder="+256..." required></div>
                <div><label>Salary (Monthly)</label><input type="number" name="salary" placeholder="0.00"></div>
                <div><label>Profile Image</label><input type="file" name="photo" accept="image/*" style="padding: 10px;"></div>
                <button type="submit" name="add_staff" style="background:var(--primary); color:white; border:none; padding:15px 25px; border-radius:16px; cursor:pointer; font-weight:800; transition:0.3s; box-shadow: 0 10px 20px rgba(39, 174, 96, 0.2);">Hire Staff</button>
            </form>
        </section>

        <div class="staff-grid" id="staffContainer">
            <?php while($s = $staff_members->fetch_assoc()): ?>
                <div class="staff-card">
                    <a href="edit_staff.php?id=<?php echo $s['id']; ?>" class="edit-btn" title="Edit Profile">
                        <i class="fas fa-pen-to-square"></i>
                    </a>

                    <a href="staff.php?delete_id=<?php echo $s['id']; ?>" class="delete-btn" onclick="return confirm('Permanently remove <?php echo $s['name']; ?> from system?')">
                        <i class="fas fa-trash-can"></i>
                    </a>

                    <div class="avatar">
                        <?php if (!empty($s['photo']) && file_exists("uploads/" . $s['photo'])): ?>
                            <img src="uploads/<?php echo $s['photo']; ?>" alt="Profile">
                        <?php else: ?>
                            <i class="fas fa-user-tie"></i>
                        <?php endif; ?>
                    </div>
                    
                    <?php 
                        $status = $s['status'] ?? 'Active'; 
                        $phone = $s['phone'] ?? '';
                    ?>

                    <span class="status-badge <?php echo ($status == 'Active') ? 'status-active' : 'status-off'; ?>">
                        <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 5px;"></i> <?php echo $status; ?>
                    </span>

                    <h2 class="staff-name" style="margin: 10px 0 5px; font-weight: 800; font-size: 1.4rem; letter-spacing: -0.5px;"><?php echo htmlspecialchars($s['name']); ?></h2>
                    <p style="color: var(--primary); font-weight: 700; font-size: 0.85rem; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px;"><?php echo htmlspecialchars($s['role']); ?></p>
                    
                    <div style="display: flex; justify-content: center; gap: 15px; margin-bottom: 5px;">
                        <a href="tel:<?php echo $phone; ?>" class="btn-call" title="Voice Call"><i class="fas fa-phone-flip"></i></a>
                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $phone); ?>" class="btn-call" style="background:#25d366;" title="WhatsApp Messenger"><i class="fab fa-whatsapp"></i></a>
                    </div>

                    <div class="payroll-box">
                        <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700;">MONTHLY SALARY</span>
                        <span style="font-weight: 800; color: var(--dark);"><?php echo number_format($s['salary'] ?? 0); ?>/-</span>
                    </div>
                    
                    <div style="margin-top: 15px;">
                        <a href="staff.php?toggle_id=<?php echo $s['id']; ?>&current=<?php echo $status; ?>" style="font-size: 0.75rem; text-decoration: none; color: var(--accent); font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                            <?php echo ($status == 'Active') ? 'Mark as Off-duty' : 'Mark as Active'; ?>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function filterStaff() {
            let input = document.getElementById('staffSearch').value.toLowerCase();
            let cards = document.getElementsByClassName('staff-card');
            for (let i = 0; i < cards.length; i++) {
                let name = cards[i].querySelector('.staff-name').innerText.toLowerCase();
                cards[i].style.display = name.includes(input) ? "block" : "none";
            }
        }
    </script>
</body>
</html>