<?php
require_once 'auth.php';
require_once 'db.php';

// --- NEW FEATURE: DATABASE BACKUP ENGINE ---
if (isset($_POST['download_backup'])) {
    $tables = array();
    $result = $conn->query("SHOW TABLES");
    while($row = $result->fetch_row()) { $tables[] = $row[0]; }
    $sql_dump = "-- Green Acres Backup\n-- Dev: SSAAZI MARTIN\n-- Date: " . date('Y-m-d H:i:s') . "\n\n";
    foreach($tables as $table) {
        $res = $conn->query("SELECT * FROM $table");
        $num_fields = $res->field_count;
        $sql_dump .= "DROP TABLE IF EXISTS $table;\n";
        $row2 = $conn->query("SHOW CREATE TABLE $table")->fetch_row();
        $sql_dump .= $row2[1].";\n\n";
        while($row = $res->fetch_row()) {
            $sql_dump .= "INSERT INTO $table VALUES(";
            for($j=0; $j<$num_fields; $j++) {
                $row[$j] = addslashes($row[$j]);
                $sql_dump .= isset($row[$j]) ? '"'.$row[$j].'"' : 'NULL';
                if ($j<($num_fields-1)) { $sql_dump .= ','; }
            }
            $sql_dump .= ");\n";
        }
        $sql_dump .= "\n\n";
    }
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="green_acres_backup_'.date('Ymd').'.sql"');
    echo $sql_dump; exit();
}

// --- NEW FEATURE: MAINTENANCE MODE TOGGLE ---
$maintenance_file = '.maintenance';
if (isset($_POST['toggle_maintenance'])) {
    file_exists($maintenance_file) ? unlink($maintenance_file) : file_put_contents($maintenance_file, 'active');
    header("Location: " . $_SERVER['PHP_SELF']); exit();
}
$is_maintenance = file_exists($maintenance_file);

// --- NEW FEATURE: LOG CLEAR ---
if (isset($_POST['clear_logs'])) {
    if (file_exists('error_log')) { file_put_contents('error_log', ''); }
    header("Location: " . $_SERVER['PHP_SELF']); exit();
}

// --- DYNAMIC DATABASE LOGIC ---
function getCount($conn, $table) {
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if($check && $check->num_rows > 0) {
        $res = $conn->query("SELECT COUNT(*) AS total FROM $table");
        return $res->fetch_assoc()['total'] ?? 0;
    }
    return 0;
}

// DYNAMIC LOG READER FOR DEVELOPER PANEL
function getSystemLogs() {
    $logFile = 'error_log'; 
    if (file_exists($logFile) && filesize($logFile) > 0) {
        $logs = file($logFile);
        $last_logs = array_slice($logs, -10); 
        return implode("<br>", array_map('htmlspecialchars', $last_logs));
    }
    return "No system errors reported. System Healthy.";
}

// FETCH REAL HARVEST DATA FOR CHART (Last 6 Months)
$months = [];
$yields = [];
$chart_query = "SELECT 
                    DATE_FORMAT(harvest_date, '%b') as m, 
                    SUM(quantity) as total 
                FROM harvest_logs 
                WHERE harvest_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY MONTH(harvest_date) 
                ORDER BY harvest_date ASC";

$chart_result = $conn->query($chart_query);
if($chart_result && $chart_result->num_rows > 0) {
    while($row = $chart_result->fetch_assoc()) {
        $months[] = $row['m'];
        $yields[] = $row['total'];
    }
} else {
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    $yields = [0, 0, 0, 0, 0, 0];
}

$animals   = getCount($conn, 'animals');
$birds     = getCount($conn, 'birds'); 
$crops     = getCount($conn, 'crops');
$produce   = getCount($conn, 'produce');
$staff     = getCount($conn, 'staff');
$suppliers = getCount($conn, 'suppliers');

$username = $_SESSION['username'] ?? 'Manager';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Acres | Command Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary: #27ae60; --secondary: #3498db; --dark: #1e272e;
            --light: #f4f7f6; --white: #ffffff; --shadow: 0 10px 40px rgba(0,0,0,0.12);
            --purple: #9b59b6; --red: #e74c3c;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; 
            display: flex; 
            background: linear-gradient(rgba(30, 39, 46, 0.6), rgba(30, 39, 46, 0.6)), 
                        url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            color: #2d3436; 
            min-height: 100vh;
        }

        .sidebar { 
            width: 260px; 
            background: rgba(30, 39, 46, 0.85); 
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            height: 100vh; 
            position: fixed; 
            color: #d1d8e0; 
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 100;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand { padding: 35px 20px; text-align: center; font-size: 1.5rem; font-weight: 800; color: var(--primary); letter-spacing: -1px; }
        .sidebar nav { flex-grow: 1; }
        .sidebar a { display: flex; align-items: center; padding: 14px 28px; color: #a5b1c2; text-decoration: none; transition: 0.3s; border-left: 4px solid transparent; cursor: pointer; }
        .sidebar a:hover, .sidebar a.active { background: rgba(39, 174, 96, 0.2); color: var(--white); border-left-color: var(--primary); }
        .sidebar a i { margin-right: 15px; width: 20px; }

        .dev-credit {
            background: rgba(39, 174, 96, 0.1);
            margin: 15px;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid rgba(39, 174, 96, 0.3);
            text-align: center;
        }
        .dev-credit span { display: block; font-size: 0.6rem; text-transform: uppercase; color: #7f8c8d; }
        .dev-credit p { margin: 2px 0; font-size: 0.8rem; font-weight: 700; color: var(--white); }
        .dev-credit a { padding: 0 !important; display: inline !important; color: var(--primary) !important; font-size: 0.75rem !important; background: none !important; border: none !important; }

        .main { margin-left: 260px; padding: 40px; width: calc(100% - 260px); animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; color: white; }

        .header-widgets { display: flex; gap: 20px; align-items: center; }
        .widget-glass { 
            background: rgba(255, 255, 255, 0.1); 
            backdrop-filter: blur(10px); 
            padding: 10px 20px; 
            border-radius: 15px; 
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { 
            background: rgba(255, 255, 255, 0.85); 
            backdrop-filter: blur(8px);
            padding: 25px; 
            border-radius: 24px; 
            box-shadow: var(--shadow); 
            position: relative; 
            overflow: hidden; 
            transition: 0.3s; 
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.4);
        }
        .stat-card:hover { transform: translateY(-5px); background: rgba(255, 255, 255, 0.95); }
        .stat-card h3 { font-size: 0.7rem; text-transform: uppercase; color: #7f8c8d; letter-spacing: 1.5px; margin: 0; }
        .stat-card .value { font-size: 2rem; font-weight: 800; margin: 10px 0; color: var(--dark); }
        .stat-card i.bg-icon { position: absolute; right: -10px; bottom: -10px; font-size: 4rem; opacity: 0.1; color: var(--dark); }

        .content-grid { display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 30px; }
        .panel { 
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: blur(10px);
            padding: 30px; 
            border-radius: 28px; 
            box-shadow: var(--shadow);
            border: 1px solid rgba(255,255,255,0.4);
        }

        .btn { padding: 12px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; border: none; transition: 0.3s; display: inline-flex; align-items: center; gap: 10px; text-decoration: none; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-danger { background: var(--red); color: white; }

        /* DEV MODAL STYLES */
        #devModal {
            display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85); backdrop-filter: blur(8px);
        }
        .modal-content {
            background: #1e272e; color: #ecf0f1; margin: 5% auto; padding: 40px;
            width: 75%; border-radius: 24px; border: 1px solid #27ae60;
            box-shadow: 0 0 50px rgba(39, 174, 96, 0.2);
        }
        .log-box {
            background: #000; color: #2ecc71; font-family: 'Courier New', monospace;
            padding: 20px; border-radius: 12px; height: 250px; overflow-y: auto; font-size: 0.8rem;
            line-height: 1.5; border: 1px solid #333;
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-leaf"></i> GREEN ACRES</div>
        <nav style="margin-top:20px;">
            <a href="#" class="active"><i class="fas fa-home"></i> Overview</a>
            <a href="suppliers.php"><i class="fas fa-truck-ramp-box"></i> Suppliers</a> 
            <a href="animals.php"><i class="fas fa-paw"></i> Livestock</a>
            <a href="birds.php"><i class="fas fa-dove"></i> Poultry</a> 
            <a href="crops.php"><i class="fas fa-seedling"></i> Crop Cycles</a>
            <a href="produce.php"><i class="fas fa-warehouse"></i> Inventory</a>
            <a href="staff.php"><i class="fas fa-user-shield"></i> Security & Team</a>
            
            <a onclick="toggleDevModal()" style="color:var(--primary);"><i class="fas fa-terminal"></i> System Dev</a>
            
            <a href="logout.php" style="margin-top:20px; color:#ff4757;"><i class="fas fa-power-off"></i> Logout</a>
        </nav>

        <div class="dev-credit">
            <span>System Developer</span>
            <p>SSAAZI MARTIN</p>
            <a href="tel:+256776955433"><i class="fas fa-phone"></i> +256 776 955 433</a>
        </div>
    </aside>

    <main class="main">
        <header>
            <div>
                <h1 style="margin:0; font-weight:800; font-size: 2.2rem; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">Hello, <?php echo $username; ?> 👋</h1>
                <p style="color:rgba(255,255,255,0.9); font-weight: 500;">
                    Status: <?php echo $is_maintenance ? '<span style="color:var(--red)">Maintenance Mode Active</span>' : 'System Live'; ?>
                </p>
            </div>
            
            <div class="header-widgets">
                <div class="widget-glass" style="border: 1px solid var(--primary); background: rgba(39, 174, 96, 0.1);">
                    <i class="fas fa-code-branch" style="color:var(--primary);"></i>
                    <span>Dev Support: +256 776 955 433 - Ssaazi Martin</span>
                </div>
                <div class="widget-glass" id="weatherWidget">
                    <i class="fas fa-sun" style="color:#f1c40f;"></i>
                    <span>24°C • Sunny</span>
                </div>
                <div class="widget-glass">
                    <i class="far fa-clock"></i>
                    <span id="liveClock">00:00:00</span>
                </div>
            </div>
        </header>

        <div class="stat-grid">
            <a href="animals.php" class="stat-card">
                <h3>Total Animals</h3>
                <div class="value"><?php echo number_format($animals); ?></div>
                <i class="fas fa-cow bg-icon"></i>
            </a>
            <a href="birds.php" class="stat-card">
                <h3>Total Birds</h3>
                <div class="value"><?php echo number_format($birds); ?></div>
                <i class="fas fa-dove bg-icon" style="color:var(--red); opacity:0.05;"></i>
            </a>
            <a href="crops.php" class="stat-card">
                <h3>Active Crops</h3>
                <div class="value"><?php echo number_format($crops); ?></div>
                <i class="fas fa-seedling bg-icon"></i>
            </a>
            <a href="produce.php" class="stat-card">
                <h3>Warehouse</h3>
                <div class="value"><?php echo number_format($produce); ?></div>
                <i class="fas fa-box bg-icon"></i>
            </a>
            <a href="suppliers.php" class="stat-card">
                <h3>Suppliers</h3>
                <div class="value"><?php echo number_format($suppliers); ?></div>
                <i class="fas fa-truck-ramp-box bg-icon"></i>
            </a>
        </div>

        <div class="content-grid">
            <div class="panel">
                <h3 style="margin: 0 0 20px 0;">Yield Velocity</h3>
                <div style="height: 350px;"><canvas id="harvestChart"></canvas></div>
            </div>
            <div class="panel">
                <h3 style="margin: 0 0 20px 0;">Asset Composition</h3>
                <div style="height: 300px;"><canvas id="assetPie"></canvas></div>
            </div>
        </div>
    </main>

    <div id="devModal">
        <div class="modal-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
                <h2 style="margin:0; color:var(--primary); text-transform:uppercase; letter-spacing:2px;"><i class="fas fa-terminal"></i> Dev Control Center</h2>
                <button onclick="toggleDevModal()" style="background:none; border:none; color:white; font-size:2rem; cursor:pointer;">&times;</button>
            </div>
            <div style="display:grid; grid-template-columns: 1fr 1.5fr; gap:40px;">
                <div>
                    <h3 style="border-bottom: 1px solid #333; padding-bottom:10px;">Environment Info</h3>
                    <p><i class="fas fa-microchip"></i> Server: <span style="color:var(--primary)">Online</span></p>
                    <p><i class="fas fa-database"></i> DB Sync: <span style="color:var(--primary)">Active</span></p>
                    <p><i class="fas fa-user-gear"></i> Dev: SSAAZI MARTIN</p>
                    <p><i class="fas fa-phone"></i> Contact: +256 776 955 433</p>
                    
                    <div style="margin-top:30px;">
                        <form method="POST">
                            <button type="submit" name="download_backup" class="btn btn-primary" style="width:100%; margin-bottom:10px;">
                                <i class="fas fa-download"></i> Full Database Backup (.sql)
                            </button>
                            <button type="submit" name="toggle_maintenance" class="btn btn-warning" style="width:100%; margin-bottom:10px;">
                                <i class="fas fa-shield-halved"></i> <?php echo $is_maintenance ? 'Disable Maintenance' : 'Enable Maintenance'; ?>
                            </button>
                            <button type="submit" name="clear_logs" class="btn btn-danger" style="width:100%;">
                                <i class="fas fa-trash-can"></i> Clear Error Logs
                            </button>
                        </form>
                    </div>
                </div>
                <div>
                    <h3 style="border-bottom: 1px solid #333; padding-bottom:10px;">Live Error Tracking</h3>
                    <div class="log-box">
                        <?php echo getSystemLogs(); ?>
                        <br>[<?php echo date('H:i:s'); ?>] Log monitor listening...
                    </div>
                    <p style="font-size:0.75rem; color:#7f8c8d; margin-top:10px;">Showing last 10 system entries from the server error log.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDevModal() {
            const modal = document.getElementById('devModal');
            modal.style.display = (modal.style.display === 'block') ? 'none' : 'block';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('devModal');
            if (event.target == modal) { modal.style.display = "none"; }
        }

        function updateClock() {
            const now = new Date();
            document.getElementById('liveClock').textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        setInterval(updateClock, 1000);
        updateClock();

        new Chart(document.getElementById('harvestChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Yield (kg)',
                    data: <?php echo json_encode($yields); ?>,
                    borderColor: '#27ae60',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(39, 174, 96, 0.1)'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        new Chart(document.getElementById('assetPie'), {
            type: 'doughnut',
            data: {
                labels: ['Livestock', 'Birds', 'Crops', 'Produce'],
                datasets: [{
                    data: [<?php echo "$animals, $birds, $crops, $produce"; ?>],
                    backgroundColor: ['#1e272e', '#e74c3c', '#27ae60', '#3498db'],
                    borderWidth: 0
                }]
            },
            options: { cutout: '75%', plugins: { legend: { position: 'bottom' } } }
        });
    </script>
</body>
</html>