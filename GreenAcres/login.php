<?php
session_start();
require 'connect.php';

$message = '';

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try{

        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user){

            if(password_verify($password, $user['password'])){

                // SAVE SESSION DATA
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit();

            }else{
                $message = "Invalid email or password";
            }

        }else{
            $message = "Invalid email or password";
        }

    }catch(PDOException $e){
        $message = "Database error.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<style>

body{
    font-family:Arial;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    margin:0;

    background:
    linear-gradient(rgba(0,50,0,0.4), rgba(0,50,0,0.4)),
    url("https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1350&q=80");
    background-size:cover;
    background-position:center;
}

.login-box{
    background: rgba(255,255,255,0.9);
    padding:40px;
    width:350px;
    border-radius:12px;
    box-shadow:0 15px 25px rgba(0,0,0,0.4);
    text-align:center;
}

.login-box h2{
    margin-bottom:25px;
    color:#2c3e50;
    font-size:28px;
}

input{
    width:100%;
    padding:12px;
    margin:12px 0;
    border-radius:6px;
    border:1px solid #ccc;
    font-size:15px;
}

input:focus{
    border-color:#28a745;
    outline:none;
}

button{
    width:100%;
    padding:12px;
    background:#28a745;
    border:none;
    color:white;
    font-size:16px;
    border-radius:6px;
    cursor:pointer;
}

button:hover{
    background:#218838;
}

a{
    color:#28a745;
    text-decoration:none;
    font-weight:bold;
}

a:hover{
    text-decoration:underline;
}

.message{
    color:#d9534f;
    font-size:14px;
}

</style>
</head>

<body>

<div class="login-box">

<h2>Login</h2>

<?php if(!empty($message)): ?>
<p class="message"><?php echo $message; ?></p>
<?php endif; ?>

<form method="POST">

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit">Login</button>

</form>

<p style="font-size:14px;">
Don't have an account? <a href="signup.php">Signup</a>
</p>

</div>

</body>
</html>
