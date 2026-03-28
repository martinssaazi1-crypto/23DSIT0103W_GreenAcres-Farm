<?php
require_once 'auth.php';
require_once 'db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: staff.php");
    exit();
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM staff WHERE id = $id");
$staff = $res->fetch_assoc();

if (!$staff) {
    header("Location: staff.php");
    exit();
}

// --- LOGIC: UPDATE STAFF ---
if (isset($_POST['update_staff'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $salary = floatval($_POST['salary']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $photo_name = $staff['photo']; // Keep old photo by default

    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        // Remove old photo if it exists
        if (!empty($staff['photo']) && file_exists($target_dir . $staff['photo'])) {
            unlink($target_dir . $staff['photo']);
        }

        $file_ext = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
        $photo_name = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "", $name) . "." . $file_ext;
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $photo_name);
    }

    $stmt = $conn->prepare("UPDATE staff SET name=?, role=?, phone=?, salary=?, status=?, photo=? WHERE id=?");
    $stmt->bind_param("sssdssi", $name, $role, $phone, $salary, $status, $photo_name, $id);
    
    if ($stmt->execute()) {
        header("Location: staff.php?status=updated");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Staff | Green Acres</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #27ae60; --dark: #1e272e; --light: #f4f7f6; --white: #ffffff; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; background: var(--light); display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .edit-card { background: var(--white); padding: 40px; border-radius: 25px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 8px; color: #4b5e6d; }
        input, select { width: 100%; padding: 12px; border: 1px solid #eee; border-radius: 12px; background: #f9f9f9; box-sizing: border-box; font-family: inherit; font-size: 1rem; }
        .btn-save { background: var(--primary); color: white; border: none; padding: 15px; width: 100%; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 1rem; margin-top: 10px; transition: 0.3s; }
        .btn-save:hover { background: #219150; transform: translateY(-2px); }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #7f8c8d; text-decoration: none; font-size: 0.9rem; }
        .current-photo { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; border: 2px solid var(--primary); }
    </style>
</head>
<body>

    <div class="edit-card">
        <h2 style="margin-top:0; color: var(--dark);">Update Staff Profile</h2>
        <p style="color: #7f8c8d; margin-bottom: 30px;">Editing: <strong><?php echo htmlspecialchars($staff['name']); ?></strong></p>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($staff['name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <?php 
                    $roles = ["Farm Manager", "Vet Technician", "Field Hand", "Security", "Driver"];
                    foreach($roles as $r) {
                        $selected = ($staff['role'] == $r) ? "selected" : "";
                        echo "<option $selected>$r</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($staff['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label>Salary (UGX)</label>
                <input type="number" name="salary" value="<?php echo $staff['salary']; ?>">
            </div>

            <div class="form-group">
                <label>Work Status</label>
                <select name="status">
                    <option value="Active" <?php if($staff['status'] == 'Active') echo 'selected'; ?>>Active</option>
                    <option value="Off-duty" <?php if($staff['status'] == 'Off-duty') echo 'selected'; ?>>Off-duty</option>
                </select>
            </div>

            <div class="form-group">
                <label>Profile Photo</label>
                <?php if($staff['photo']): ?>
                    <img src="uploads/<?php echo $staff['photo']; ?>" class="current-photo"><br>
                <?php endif; ?>
                <input type="file" name="photo" accept="image/*">
                <small style="color: #95a5a6;">Leave blank to keep current photo</small>
            </div>

            <button type="submit" name="update_staff" class="btn-save">Save Changes</button>
            <a href="staff.php" class="back-link">Cancel and Go Back</a>
        </form>
    </div>

</body>
</html>