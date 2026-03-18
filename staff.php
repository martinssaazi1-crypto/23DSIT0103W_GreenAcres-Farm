<?php
include "db.php";

if(isset($_POST['add'])){
$name=$_POST['name'];
$role=$_POST['role'];

$conn->query("INSERT INTO staff(name,role) VALUES('$name','$role')");
}

if(isset($_GET['delete'])){
$id=$_GET['delete'];
$conn->query("DELETE FROM staff WHERE id=$id");
}

if(isset($_POST['update'])){
$id=$_POST['id'];
$name=$_POST['name'];
$role=$_POST['role'];

$conn->query("UPDATE staff SET name='$name', role='$role' WHERE id=$id");
}

$result=$conn->query("SELECT * FROM staff");
?>

<!DOCTYPE html>
<html>
<head>
<title>Farm Staff</title>

<style>

/* ================= GLOBAL ================= */
body{
font-family:Arial, Helvetica, sans-serif;
margin:0;
background: linear-gradient(to bottom, #f1f8e9, #ffffff);
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

/* ================= STAFF IMAGE GALLERY ================= */
.gallery{
display:flex;
justify-content:center;
flex-wrap:wrap;
gap:20px;
margin-bottom:30px;
}

.gallery img{
width:120px;
height:120px;
object-fit:cover;
border-radius:50%;
border:4px solid #66CDAA;
box-shadow:0 4px 12px rgba(0,0,0,0.25);
transition: transform 0.3s;
.staff-card{
text-align:center;
}

.staff-card p{
margin-top:8px;
font-weight:bold;
color:#2e7d32;
font-size:14px;
}
}

.gallery img:hover{
transform:scale(1.1);
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
.gallery img{
width:90px;
height:90px;
}
}
</style>

</head>
<body>

<header>
<h1>GREEN ACRES Farm Staff</h1>
</header>

<div class="container">

<!-- Staff Image Gallery -->
<div class="gallery">

<div class="staff-card">
<img src="assets/images/martin.jpg" alt="Farm Manager">
<p>Martin – Accountant</p>
</div>

<div class="staff-card">
<img src="assets/images/live stock care taker.jpg" alt="Livestock Caretaker">
<p>Sarah – Livestock Caretaker</p>
</div>

<div class="staff-card">
<img src="assets/images/manager.jpg" alt="Manager">
<p>Sarah – Assistant Farm Manager</p>
</div>

<div class="staff-card">
<img src="assets/images/farm assistant.jpg" alt="Farm Assistant">
<p>Jack – Farm Assistant</p>
</div>

<div class="staff-card">
<img src="assets/images/crop specialist.jpg" alt="Crop Specialist">
<p>Paul – Crop Specialist</p>
</div>

</div>

<h2>Green Acres Staff</h2>

<form method="POST">
Name <input type="text" name="name">
Role <input type="text" name="role">
<button name="add">Add Staff</button>
</form>

<table border="1">

<tr>
<th>ID</th>
<th>Name</th>
<th>Role</th>
<th>Action</th>
</tr>

<?php while($row=$result->fetch_assoc()){ ?>

<tr>

<form method="POST">

<td><?php echo $row['id']; ?></td>

<td>
<input type="text" name="name" value="<?php echo $row['name']; ?>">
</td>

<td>
<input type="text" name="role" value="<?php echo $row['role']; ?>">
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