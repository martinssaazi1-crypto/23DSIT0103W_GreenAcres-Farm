<?php
include "db.php";

if(isset($_POST['add'])){
$name=$_POST['crop_name'];
$q=$_POST['quantity']; 

$conn->query("INSERT INTO crops(crop_name,quantity) VALUES('$name','$q')");
}

if(isset($_GET['delete'])){
$id=$_GET['delete'];
$conn->query("DELETE FROM crops WHERE id=$id");
}

if(isset($_POST['update'])){
$id=$_POST['id'];
$name=$_POST['crop_name'];
$q=$_POST['quantity'];

$conn->query("UPDATE crops SET crop_name='$name', quantity='$q' WHERE id=$id");
}

$result=$conn->query("SELECT * FROM crops");
?>

<!DOCTYPE html>
<html>
<head>
<title>Farm Crops</title>

<style>

/* ================= GLOBAL ================= */
body{
font-family:Arial, Helvetica, sans-serif;
margin:0;
background: linear-gradient(to bottom, #e8f5e9, #ffffff);
color:#333;
}

/* ================= HEADER ================= */
header{
background:#66CDAA;
color:white;
text-align:center;
padding:25px 20px;
box-shadow:0 4px 10px rgba(0,0,0,0.2);
}

header h1{
margin:0;
}

/* ================= CONTAINER ================= */
.container{
width:90%;
max-width:1000px;
margin:auto;
padding:20px;
}

/* ================= CROP IMAGE GALLERY ================= */
.gallery{
display:flex;
justify-content:center;
flex-wrap:wrap;
gap:20px;
margin-bottom:30px;
}

.gallery img{
width:160px;
height:120px;
object-fit:cover;
border-radius:10px;
box-shadow:0 4px 12px rgba(0,0,0,0.25);
transition: transform 0.3s;
}

.gallery img:hover{
transform:scale(1.05);
}

/* ================= FORM ================= */
form{
background:white;
padding:20px;
border-radius:10px;
box-shadow:0 4px 12px rgba(0,0,0,0.2);
margin-bottom:30px;
display:flex;
flex-wrap:wrap;
gap:15px;
align-items:center;
}

form input{
padding:8px;
border-radius:5px;
border:1px solid #ccc;
flex:1 1 200px;
}

form button{
padding:10px 16px;
background:#28a745;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
transition:0.3s;
}

form button:hover{
background:#1f7a34;
}

/* ================= TABLE ================= */
table{
width:100%;
border-collapse:collapse;
background:white;
box-shadow:0 4px 12px rgba(0,0,0,0.2);
border-radius:10px;
overflow:hidden;
}

table th{
background:#66CDAA;
color:white;
padding:12px;
text-align:center;
}

table td{
padding:10px;
text-align:center;
border-bottom:1px solid #ddd;
}

table tr:nth-child(even){
background:#f2f9f9;
}

a{
color:red;
text-decoration:none;
font-weight:bold;
}

a:hover{
text-decoration:underline;
}

/* ================= BACK LINK ================= */
.back{
display:block;
margin-top:20px;
text-align:center;
font-weight:bold;
font-size:18px;
text-decoration:none;
color:#333;
}

.back:hover{
color:#28a745;
}

/* ================= RESPONSIVE ================= */
@media(max-width:600px){
form{
flex-direction:column;
align-items:flex-start;
}
}
</style>

</head>
<body>

<header>
<h1>Farm Crops</h1>
</header>

<div class="container">

<!-- Crop Images Gallery -->
<div class="gallery">
<img src="assets/images/tomatoes.jpg" alt="tomatoes">
<img src="assets/images/spinach.jpg" alt="spinach">
<img src="assets/images/maize.jpg" alt="Maize">
<img src="assets/images/carrots.jpg" alt="Carrots">
</div>

<h2>Crops</h2>

<form method="POST">
Crop Name <input type="text" name="crop_name">
Quantity <input type="number" name="quantity">
<button name="add">Add Crop</button>
</form>

<table border="1">

<tr>
<th>ID</th>
<th>Name</th>
<th>Quantity</th>
<th>Action</th>
</tr>

<?php while($row=$result->fetch_assoc()){ ?>

<tr>

<form method="POST">

<td><?php echo $row['id']; ?></td>

<td>
<input type="text" name="crop_name" value="<?php echo $row['crop_name']; ?>">
</td>

<td>
<input type="number" name="quantity" value="<?php echo $row['quantity']; ?>">
</td>

<td>

<input type="hidden" name="id" value="<?php echo $row['id']; ?>">

<button name="update">Update</button>

<a href="?delete=<?php echo $row['id']; ?>">Delete</a>

</td>

</form>

</tr>

<?php } ?>

</table>

<a class="back" href="dashboard.php">
⬅ Back to Dashboard
</a>

</div>

</body>
</html>