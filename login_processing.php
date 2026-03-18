<?php

session_start();
include "db.php";

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username='$username'";

$result = $conn->query($sql);

if($result->num_rows > 0){

$user = $result->fetch_assoc();

if(password_verify($password,$user['password'])){

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

header("Location: dashboard.php");
exit();

}else{

echo "Invalid Password";

}

}else{

echo "User not found";

}

?>