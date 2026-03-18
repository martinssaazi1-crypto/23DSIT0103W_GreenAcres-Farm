<?php

$conn = new mysqli("localhost","root","","farm_supply_green");

if($conn->connect_error){
die("Connection failed: " . $conn->connect_error);
}

?>