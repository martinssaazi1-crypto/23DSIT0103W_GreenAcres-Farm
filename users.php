<?php
require_once 'auth.php';
require_once 'db.php';
// Only admin can access this pagerequire_role(['admin']);
$message = '';
$message_type = '';
// Handle user actions
if (isset($_POST['action'])) {
    $user_id = $_POST['user_id'];
   
    if ($_POST['action'] == 'delete' && $user_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $message = "User deleted successfully.";
            $message_type = 'success';
        }
    }
   
    if ($_POST['action'] == 'update_role') {
        $new_role = $_POST['role'];
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        if ($stmt->execute()) {
            $message = "User role updated successfully.";
            $message_type = 'success';
        }
    }
}
// Fetch all users
$users = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Green Acres</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-aweso
me/6.4.0/css/all.min.css">
<style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
       
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
       
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #27ae60 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
       
        .header h1 { font-size: 24px; }
        .header h1 span { color: #f39c12; }
       
        .back-btn {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            transition: all 0.3s;
        }
       
        .back-btn:hover { background: rgba(255,255,255,0.3); }
       
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
       
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
       
        .card-header {
            background: #27ae60;
            color: white;
 padding: 20px;
        }
       
        .card-header h2 { font-size: 20px; }
       
        .message {
            padding: 12px;
            margin: 20px;
            border-radius: 8px;
        }
       
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
       
        table {
            width: 100%;
            border-collapse: collapse;
        }
       
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
       
        th {
            background: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
        }
       
        tr:hover { background: #f8f9fa; }
       
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
       
        .role-admin {
            background: #f39c12;
            color: white;
}
       
        .role-user {
            background: #3498db;
            color: white;
        }
       
        select {
            padding: 6px 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
       
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s;
        }
       
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
       
        .btn-danger:hover {
            background: #c0392b;
        }
       
        .btn-primary {
            background: #27ae60;
            color: white;
        }
       
        .btn-primary:hover {
            background: #229954;
        }
       
        .inline-form {
            display: inline;
        }
    </style>
</head>
<body>
    <div class="header">
<h1>🌾 Green <span>Acres</span> - User Management</h1>
        <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i>
Back to Dashboard</a>
    </div>
   
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-users"></i> System Users</h2>
            </div>
           
            <?php if($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
           
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td
>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo $user['role'];
?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <?php if($user['id'] != $_SESSION['user_id']): ?>
                            <form method="POST" class="inline-form" style="display: inline-block;">
<input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <select name="role" onchange="this.form.submit()">
                                    <option value="user" <?php echo $user['role']
== 'user' ? 'selected' : ''; ?>>User</option>
                                    <option value="admin" <?php echo $user['role']
== 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <input type="hidden" name="action" value="update_r
ole">
                            </form>
                           
                            <form method="POST" class="inline-form" style="display: inline-block;"
                                  onsubmit="return confirm('Are you sure you wantto delete this user?');">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                            <?php else: ?>
                            <span style="color: #95a5a6;">(Current User)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>