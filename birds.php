<?php
require_once 'auth.php';
require_once 'db.php';

// --- LOGIC: ADD NEW BIRD FLOCK ---
if (isset($_POST['add_bird'])) {
    $breed = mysqli_real_escape_string($conn, $_POST['breed']);
    $type = mysqli_real_escape_string($conn, $_POST['type']); 
    $quantity = intval($_POST['quantity']);
    $age = intval($_POST['age']); 
    $health = mysqli_real_escape_string($conn, $_POST['health']);

    $sql = "INSERT INTO birds (breed, type, quantity, age_weeks, health_status) 
            VALUES ('$breed', '$type', $quantity, $age, '$health')";
    if ($conn->query($sql)) {
        header("Location: birds.php?status=success");
    }
}

// --- LOGIC: DELETE FLOCK ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM birds WHERE id = $id");
    header("Location: birds.php?status=deleted");
}

$flocks = $conn->query("SELECT * FROM birds ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Poultry Hub | Green Acres</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #27ae60; --birds: #e74c3c; --dark: #1e272e; --white: #ffffff; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; display: flex; 
            background: linear-gradient(rgba(30, 39, 46, 0.7), rgba(30, 39, 46, 0.7)), 
                        url('https://images.unsplash.com/photo-1516211697506-8360bd7734b7?q=80&w=2000&auto=format&fit=crop');
            background-size: cover; background-attachment: fixed; min-height: 100vh;
        }
        .sidebar { width: 260px; background: rgba(30, 39, 46, 0.9); backdrop-filter: blur(15px); height: 100vh; position: fixed; color: #d1d8e0; border-right: 1px solid rgba(255,255,255,0.1); }
        .sidebar-brand { padding: 35px 20px; text-align: center; font-weight: 800; color: var(--primary); font-size: 1.4rem; }
        .sidebar a { display: flex; align-items: center; padding: 16px 28px; color: #a5b1c2; text-decoration: none; transition: 0.3s; }
        .sidebar a.active { background: rgba(231, 76, 60, 0.1); color: white; border-left: 4px solid var(--birds); }

        .main { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        .glass-panel { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); padding: 30px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); margin-bottom: 30px; }
        
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; align-items: end; }
        input, select { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #ddd; font-family: inherit; outline: none; }
        
        .btn-add { background: var(--birds); color: white; border: none; padding: 12px 25px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-add:hover { background: #c0392b; transform: translateY(-2px); }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; color: #7f8c8d; font-size: 0.75rem; text-transform: uppercase; padding: 15px; border-bottom: 2px solid #eee; }
        td { padding: 15px; border-bottom: 1px solid #eee; font-weight: 600; color: var(--dark); }
        
        .badge { padding: 6px 14px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        .healthy { background: #e8f5e9; color: #27ae60; }
        .warning { background: #fff3e0; color: #f39c12; }
        .danger { background: #ffebee; color: #e74c3c; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-leaf"></i> GREEN ACRES</div>
        <nav style="margin-top:20px;">
            <a href="dashboard.php"><i class="fas fa-home" style="margin-right:15px;"></i> Overview</a>
            <a href="birds.php" class="active"><i class="fas fa-dove" style="margin-right:15px;"></i> Poultry</a>
            <a href="animals.php"><i class="fas fa-paw" style="margin-right:15px;"></i> Livestock</a>
        </nav>
    </aside>

    <main class="main">
        <h1 style="color: white; font-weight: 800; margin-bottom: 5px;">Poultry Inventory</h1>
        <p style="color: rgba(255,255,255,0.7); margin-bottom: 30px;">Manage and monitor your bird flocks.</p>

        <div class="glass-panel">
            <h3 style="margin: 0 0 20px 0; color: var(--birds);">Register New Flock</h3>
            <form method="POST" class="form-grid">
                <div>
                    <label style="font-size:0.7rem; font-weight:700;">Breed</label>
                    <input type="text" name="breed" placeholder="White Leghorn" required>
                </div>
                <div>
                    <label style="font-size:0.7rem; font-weight:700;">Type</label>
                    <select name="type">
                        <option>Broiler</option>
                        <option>Layer</option>
                        <option>Kienyeji</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.7rem; font-weight:700;">Quantity</label>
                    <input type="number" name="quantity" required>
                </div>
                <div>
                    <label style="font-size:0.7rem; font-weight:700;">Age (Wks)</label>
                    <input type="number" name="age" required>
                </div>
                <div>
                    <label style="font-size:0.7rem; font-weight:700;">Health</label>
                    <select name="health">
                        <option value="Healthy">Healthy</option>
                        <option value="Quarantine">Quarantine</option>
                        <option value="Treatment">Treatment</option>
                    </select>
                </div>
                <button type="submit" name="add_bird" class="btn-add">Add Flock</button>
            </form>
        </div>

        <div class="glass-panel">
            <table>
                <thead>
                    <tr>
                        <th>Flock Detail</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Age</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $flocks->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['breed']; ?></td>
                        <td><?php echo $row['type']; ?></td>
                        <td style="font-size: 1.1rem; color: var(--birds);"><?php echo number_format($row['quantity']); ?></td>
                        <td><?php echo $row['age_weeks']; ?> Weeks</td>
                        <td>
                            <?php 
                                $statusClass = 'healthy';
                                if($row['health_status'] == 'Quarantine') $statusClass = 'warning';
                                if($row['health_status'] == 'Treatment') $statusClass = 'danger';
                            ?>
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $row['health_status']; ?></span>
                        </td>
                        <td>
                            <a href="birds.php?delete=<?php echo $row['id']; ?>" style="color:#bdc3c7;" onclick="return confirm('Remove flock?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>