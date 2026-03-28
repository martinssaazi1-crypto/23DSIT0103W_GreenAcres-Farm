<?php
require 'connect.php';
session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Default role
    $role = 'user';

    // Insert into users table
    $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashed_password,
            ':role' => $role
        ]);
        $message = "Account created successfully! <a href='login.php'>Login here</a>";
    } catch (PDOException $e) {
        $message = "Error: That email is already registered.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Sign Up</title>

<style>

body{
    margin:0;
    font-family: Arial, Helvetica, sans-serif;

    /* Background Image */
    background-image: url("https://images.unsplash.com/photo-1501004318641-b39e6451bec6");
    background-size: cover;
    background-position: center;
    height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;
}

/* Glass form effect */
.form-container{
    background: rgba(255,255,255,0.9);
    padding:40px;
    width:350px;
    border-radius:10px;
    box-shadow:0 10px 25px rgba(0,0,0,0.3);
    text-align:center;
}

h2{
    margin-bottom:20px;
    color:#2c3e50;
}

input{
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:5px;
    border:1px solid #ccc;
    font-size:14px;
}

input:focus{
    border-color:#28a745;
    outline:none;
}

/* Button styling */
button{
    width:100%;
    padding:12px;
    background:#28a745;
    border:none;
    color:white;
    font-size:16px;
    border-radius:5px;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#218838;
}

.message{
    font-size:14px;
    margin-bottom:10px;
}

.login-link{
    margin-top:15px;
    font-size:14px;
}

.login-link a{
    color:#28a745;
    text-decoration:none;
    font-weight:bold;
}
/* Add GREEN ACRES above the form without touching HTML */
.form-container::before {
    content: "GREEN ACRES";
    display: block;
    font-size: 28px;
    font-weight: bold;
    color: #28a745;
    margin-bottom: 20px;
    text-align: center;
    letter-spacing: 2px;
}
</style>
</head>

<body>

<div class="form-container">

<h2>Create Account</h2>

<?php if($message) echo "<p class='message'>$message</p>"; ?>

<form method="POST" action="signup.php">

<input type="text" name="username" placeholder="Username" required>

<input type="email" name="email" placeholder="Email Address" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit">Create Account</button>

</form>

<div class="login-link">
Already have an account? <a href="login.php">Login</a>
</div>

</div>

</body>
</html>