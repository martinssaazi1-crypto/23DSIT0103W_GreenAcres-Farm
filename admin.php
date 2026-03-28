<?php

$conn = new mysqli("localhost","root","","green_acrea_system");

$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role = "admin";

$sql = "INSERT INTO users(username,password,role)
VALUES('$username','$password','$role')";

if($conn->query($sql)){
    echo "Admin created successfully";
}else{
    echo "Error: ".$conn->error;
}

?>