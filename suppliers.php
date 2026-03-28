<?php
require_once 'auth.php';
require_once 'db.php';

// --- LOGIC: ARCHIVE SUPPLIER (SOFT DELETE) ---
if (isset($_GET['archive_id'])) {
    $archive_id = intval($_GET['archive_id']);
    $conn->query("UPDATE suppliers SET status = 'archived' WHERE id = $archive_id");
    header("Location: suppliers.php?status=archived");
    exit();
}

// --- LOGIC: ADD NEW SUPPLIER ---
if (isset($_POST['add_supplier'])) {
    $name = mysqli_real_escape_string($conn, $_POST['supplier_name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact_person']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $category = $_POST['category'];

    $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, contact_person, phone, category, status) VALUES (?, ?, ?, ?, 'active')");
    $stmt->bind_param("ssss", $name, $contact, $phone, $category);
    $stmt->execute();
    header("Location: suppliers.php?status=added");
    exit();
}

// --- LOGIC: PROCESS ORDER & DEDUCT INVENTORY ---
if (isset($_POST['place_order'])) {
    $s_id = $_POST['supplier_id'];
    $p_id = $_POST['produce_id'];
    $qty  = intval($_POST['qty']);

    $conn->begin_transaction();
    try {
        $stmt1 = $conn->prepare("INSERT INTO supplier_orders (supplier_id, produce_id, order_quantity) VALUES (?, ?, ?)");
        $stmt1->bind_param("iii", $s_id, $p_id, $qty);
        $stmt1->execute();

        $stmt2 = $conn->prepare("UPDATE produce SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");
        $stmt2->bind_param("iii", $qty, $p_id, $qty);
        $stmt2->execute();

        if ($stmt2->affected_rows > 0) {
            $conn->commit();
            header("Location: suppliers.php?status=success");
        } else {
            $conn->rollback();
            header("Location: suppliers.php?status=insufficient");
        }
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: suppliers.php?status=error");
    }
    exit();
}

// --- DATA FETCHING ---
$suppliers = $conn->query("SELECT * FROM suppliers WHERE (status = 'active' OR status IS NULL) ORDER BY supplier_name ASC");
$produce_items = $conn->query("SELECT id, produce_name, quantity FROM produce WHERE quantity > 0");
$history = $conn->query("SELECT so.order_date, s.supplier_name, p.produce_name, so.order_quantity 
                         FROM supplier_orders so 
                         JOIN suppliers s ON so.supplier_id = s.id 
                         JOIN produce p ON so.produce_id = p.id 
                         ORDER BY so.order_date DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supply Chain | Green Acres</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { 
            --primary: #27ae60; --dark: #1e272e; --light: #f8fafb; 
            --white: #ffffff; --shadow: 0 10px 40px rgba(0,0,0,0.06); 
            --danger: #eb4d4b; --warning: #f39c12;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; 
            display: flex; 
            background: var(--light); 
            color: #2d3436; 
            overflow-x: hidden;
        }
        
        /* Sidebar Consistency */
        .sidebar { width: 260px; background: var(--dark); height: 100vh; position: fixed; color: #d1d8e0; z-index: 1000; }
        .sidebar-brand { padding: 40px 30px; text-align: center; font-size: 1.4rem; font-weight: 800; color: var(--primary); letter-spacing: -1px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar a { display: flex; align-items: center; padding: 16px 28px; color: #a5b1c2; text-decoration: none; border-left: 4px solid transparent; transition: 0.3s; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background: rgba(39, 174, 96, 0.1); color: var(--white); border-left-color: var(--primary); }
        .sidebar i { margin-right: 15px; width: 20px; font-size: 1.1rem; }

        /* Content Area */
        .main-content { margin-left: 260px; padding: 50px; width: calc(100% - 260px); animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Search Bar Refinement */
        .search-container { position: relative; width: 400px; }
        .search-container i { position: absolute; left: 20px; top: 15px; color: #b2bec3; font-size: 1rem; }
        .search-bar { 
            padding: 14px 20px 14px 55px; width: 100%; border-radius: 18px; 
            border: 2px solid #f1f3f5; background: #fff; outline: none; 
            transition: 0.3s; font-family: inherit; font-weight: 600;
        }
        .search-bar:focus { border-color: var(--primary); box-shadow: 0 10px 25px rgba(39, 174, 96, 0.1); }
        
        /* Form Card */
        .card { 
            background: var(--white); padding: 35px; border-radius: 28px; 
            box-shadow: var(--shadow); margin-bottom: 40px; border: 1px solid rgba(0,0,0,0.02);
        }
        .form-grid { display: grid; grid-template-columns: 2fr 1.5fr 1.5fr 1.2fr auto; gap: 20px; align-items: flex-end; }
        .form-grid label { display: block; font-size: 0.75rem; font-weight: 800; margin-bottom: 10px; color: #b2bec3; text-transform: uppercase; letter-spacing: 1px; }
        input, select { 
            width: 100%; padding: 14px 18px; border: 2px solid #f1f3f5; border-radius: 16px; 
            background: #f8fafc; font-family: inherit; font-weight: 600; transition: 0.3s;
        }
        input:focus, select:focus { outline: none; border-color: var(--primary); background: #fff; }

        /* Partner Cards */
        .supplier-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 25px; }
        .supplier-card { 
            background: white; padding: 30px; border-radius: 30px; box-shadow: var(--shadow); 
            border-top: 6px solid var(--primary); position: relative; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
        }
        .supplier-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(0,0,0,0.1); }
        
        .category-tag { 
            font-size: 0.65rem; text-transform: uppercase; font-weight: 800; color: var(--primary); 
            background: #e6fff0; padding: 6px 14px; border-radius: 12px; letter-spacing: 0.5px;
        }
        
        .archive-btn { 
            position: absolute; top: 25px; right: 25px; color: #cbd5e1; 
            text-decoration: none; font-size: 1.2rem; transition: 0.3s; 
        }
        .archive-btn:hover { color: var(--danger); transform: rotate(90deg); }

        /* Modal Refinement */
        .modal { display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(30, 39, 46, 0.8); backdrop-filter:blur(8px); align-items:center; justify-content:center; }
        .modal-content { 
            background:white; padding:40px; border-radius:32px; width:450px; 
            box-shadow: 0 30px 70px rgba(0,0,0,0.3); animation: modalPop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes modalPop { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        
        /* Audit Table */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 20px; color: #94a3b8; border-bottom: 2px solid #f8fafc; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; }
        td { padding: 20px; border-bottom: 1px solid #f8fafc; font-size: 0.95rem; font-weight: 600; }

        .status-msg { padding: 18px 30px; border-radius: 18px; margin-bottom: 30px; font-weight: 700; border: 1px solid rgba(0,0,0,0.05); }
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
            <a href="suppliers.php" class="active"><i class="fas fa-truck-fast"></i> Suppliers</a>
            <a href="staff.php"><i class="fas fa-users-gear"></i> Staff</a>
            <a href="logout.php" style="margin-top:50px; color:var(--danger);"><i class="fas fa-power-off"></i> Logout</a>
        </nav>
    </aside>

    <div class="main-content">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <div>
                <h1 style="margin:0; font-size: 2.4rem; font-weight: 800; letter-spacing: -1px;">Supply Chain</h1>
                <p style="color: #94a3b8; font-weight: 500;">Manage off-takers and inventory distribution.</p>
            </div>
            <div class="search-container">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" id="partnerSearch" onkeyup="filterPartners()" class="search-bar" placeholder="Search by name or category...">
            </div>
        </header>

        <?php if(isset($_GET['status'])): ?>
            <div class="status-msg" style="<?php echo (in_array($_GET['status'],['success','added'])) ? 'background:#e6fff0;color:#27ae60;' : 'background:#fff4e6;color:#e67e22;'; ?>">
                <?php 
                    if($_GET['status']=='success') echo "<i class='fas fa-check-circle'></i> Dispatch recorded. Warehouse levels updated.";
                    elseif($_GET['status']=='insufficient') echo "<i class='fas fa-exclamation-triangle'></i> Stock alert: Not enough quantity in warehouse!";
                    elseif($_GET['status']=='added') echo "<i class='fas fa-user-plus'></i> New partner registered successfully.";
                    elseif($_GET['status']=='archived') echo "<i class='fas fa-box-archive'></i> Partner successfully archived.";
                    else echo "<i class='fas fa-circle-xmark'></i> An error occurred processing your request.";
                ?>
            </div>
        <?php endif; ?>
        
        <section class="card">
            <h3 style="margin-top:0; font-weight: 800; margin-bottom: 25px;"><i class="fas fa-plus-circle" style="color:var(--primary);"></i> Add New Distribution Partner</h3>
            <form method="POST" class="form-grid">
                <div><label>Partner Name</label><input type="text" name="supplier_name" placeholder="Business Name" required></div>
                <div><label>Point of Contact</label><input type="text" name="contact_person" placeholder="Full Name"></div>
                <div><label>Phone Number</label><input type="text" name="phone" placeholder="+256..."></div>
                <div>
                    <label>Market Category</label>
                    <select name="category">
                        <option>Wholesaler</option><option>Retailer</option>
                        <option>Export Partner</option><option>Local Market</option>
                    </select>
                </div>
                <button type="submit" name="add_supplier" style="background:var(--primary); color:white; border:none; padding:15px 30px; border-radius:16px; cursor:pointer; font-weight:800; transition:0.3s;">Save Partner</button>
            </form>
        </section>

        <div class="supplier-grid" id="supplierContainer">
            <?php while($s = $suppliers->fetch_assoc()): ?>
                <div class="supplier-card">
                    <a href="suppliers.php?archive_id=<?php echo $s['id']; ?>" class="archive-btn" onclick="return confirm('Archive this partner?')"><i class="fas fa-folder-minus"></i></a>
                    <span class="category-tag"><?php echo $s['category']; ?></span>
                    <h2 class="partner-name" style="margin: 20px 0 8px; font-weight: 800; font-size: 1.5rem;"><?php echo htmlspecialchars($s['supplier_name']); ?></h2>
                    <p style="color:#94a3b8; margin-bottom: 25px; font-weight: 600;"><i class="fas fa-user-check" style="color:var(--primary); margin-right:5px;"></i> <?php echo htmlspecialchars($s['contact_person']); ?></p>
                    
                    <div style="background: #f8fafc; padding: 20px; border-radius: 20px; font-size: 0.95rem; margin-bottom: 25px; border: 1px solid #f1f3f5;">
                        <p style="margin: 0 0 10px; font-weight: 800;"><a href="tel:<?php echo $s['phone']; ?>" style="text-decoration:none;color:var(--dark);"><i class="fas fa-phone-volume" style="margin-right:10px; color:#64748b;"></i> <?php echo $s['phone']; ?></a></p>
                        <p style="margin: 0; color: #cbd5e1; font-size: 0.8rem; font-weight: 700;"><i class="fas fa-clock" style="margin-right:10px;"></i> PARTNER SINCE <?php echo strtoupper(date('M Y', strtotime($s['created_at']))); ?></p>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button onclick="openOrderModal(<?php echo $s['id']; ?>, '<?php echo addslashes($s['supplier_name']); ?>')" style="flex:1.5; padding:14px; border-radius:15px; border:2px solid #f1f3f5; background:white; cursor:pointer; font-weight:800; transition:0.3s;"><i class="fas fa-truck-loading"></i> Dispatch</button>
                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $s['phone']); ?>" target="_blank" style="flex:1; padding:14px; border-radius:15px; border:none; background:#25D366; color:white; text-decoration:none; text-align:center; font-weight:800; display:flex; align-items:center; justify-content:center; gap:8px;"><i class="fab fa-whatsapp"></i> Chat</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <section class="card" style="margin-top:50px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h3 style="margin:0; font-weight: 800;"><i class="fas fa-list-check" style="color:var(--primary); margin-right:10px;"></i> Dispatch History & Fulfillment</h3>
                <span style="background:#f1f5f9; color:#64748b; padding:8px 20px; border-radius:30px; font-size:0.7rem; font-weight:800; letter-spacing:1px;">WAREHOUSE LOGS</span>
            </div>
            <table>
                <thead>
                    <tr><th>Fulfillment Date</th><th>Partner</th><th>Produce Item</th><th>Quantity</th><th>Log Status</th></tr>
                </thead>
                <tbody>
                    <?php if($history->num_rows > 0): ?>
                        <?php while($h = $history->fetch_assoc()): ?>
                            <tr>
                                <td style="color:#94a3b8;"><?php echo date('d M Y, H:i', strtotime($h['order_date'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($h['supplier_name']); ?></strong></td>
                                <td><span style="background:#f1f5f9; padding:6px 12px; border-radius:10px; font-size:0.85rem;"><?php echo htmlspecialchars($h['produce_name']); ?></span></td>
                                <td style="font-weight:800; font-size: 1.1rem;"><?php echo $h['order_quantity']; ?> <small style="font-size:0.7rem; color:#cbd5e1;">UNITS</small></td>
                                <td style="color:var(--primary); font-weight:800;"><i class="fas fa-circle-check"></i> DISPATCHED</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding:50px; color:#cbd5e1; font-weight:700;">NO RECENT DISPATCHES FOUND</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>

    <div id="orderModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle" style="margin-top:0; font-weight:800; color:var(--dark); font-size: 1.8rem; letter-spacing:-1px;">Dispatch Stock</h2>
            <form method="POST">
                <input type="hidden" name="supplier_id" id="hidden_s_id">
                <div style="margin-bottom:20px;">
                    <label style="display:block; margin-bottom:10px; font-weight:800; font-size:0.75rem; color:#b2bec3; text-transform:uppercase;">Warehouse Inventory</label>
                    <select name="produce_id" required style="background:#f8fafc;">
                        <?php 
                        $produce_items->data_seek(0);
                        while($p = $produce_items->fetch_assoc()): ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo $p['produce_name']; ?> (Available: <?php echo $p['quantity']; ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div style="margin-bottom:30px;">
                    <label style="display:block; margin-bottom:10px; font-weight:800; font-size:0.75rem; color:#b2bec3; text-transform:uppercase;">Total Quantity</label>
                    <input type="number" name="qty" required placeholder="0.00" style="font-size: 1.2rem; font-weight: 800;">
                </div>
                <button type="submit" name="place_order" style="width:100%; background:var(--primary); color:white; border:none; padding:18px; border-radius:18px; cursor:pointer; font-weight:800; font-size:1.1rem; box-shadow: 0 10px 20px rgba(39, 174, 96, 0.2);">Authorize Dispatch</button>
            </form>
            <button onclick="closeModal()" style="width:100%; background:none; border:none; margin-top:20px; cursor:pointer; color:#94a3b8; font-weight:700;">Cancel Transaction</button>
        </div>
    </div>

    <script>
        function filterPartners() {
            let input = document.getElementById('partnerSearch').value.toLowerCase();
            let cards = document.getElementsByClassName('supplier-card');
            for (let i = 0; i < cards.length; i++) {
                let name = cards[i].querySelector('.partner-name').innerText.toLowerCase();
                let category = cards[i].querySelector('.category-tag').innerText.toLowerCase();
                cards[i].style.display = (name.includes(input) || category.includes(input)) ? "block" : "none";
            }
        }

        function openOrderModal(id, name) {
            document.getElementById('hidden_s_id').value = id;
            document.getElementById('modalTitle').innerText = "Dispatch to " + name;
            document.getElementById('orderModal').style.display = 'flex';
        }
        function closeModal() { document.getElementById('orderModal').style.display = 'none'; }
        window.onclick = function(e) { if(e.target == document.getElementById('orderModal')) closeModal(); }
    </script>
</body>
</html>