<?php
include "db.php";

if(isset($_POST['add'])){
$name=$_POST['produce_name'];
$q=$_POST['quantity'];

$conn->query("INSERT INTO produce(produce_name,quantity) VALUES('$name','$q')");
}

if(isset($_GET['delete'])){
$id=$_GET['delete'];
$conn->query("DELETE FROM produce WHERE id=$id");
}

if(isset($_POST['update'])){
$id=$_POST['id'];
$name=$_POST['produce_name'];
$q=$_POST['quantity'];

$conn->query("UPDATE produce SET produce_name='$name', quantity='$q' WHERE id=$id");
}

$result=$conn->query("SELECT * FROM produce");
?>

<!DOCTYPE html>
<html>
<head>

<title>Farm Produce</title>

<style>

body{
font-family:Arial;
margin:0;
background:#f4f7f6;
}

/* HEADER */

header{
background:#66CDAA;
color:white;
text-align:center;
padding:20px;
}

/* CONTAINER */

.container{
width:90%;
margin:auto;
padding:20px;
}

/* PRODUCE IMAGE GALLERY */

.gallery{
display:flex;
justify-content:center;
gap:20px;
flex-wrap:wrap;
margin-bottom:30px;
}

.gallery img{
width:160px;
height:120px;
object-fit:cover;
border-radius:10px;
box-shadow:0 4px 10px rgba(0,0,0,0.2);
}

/* FORM */

form{
background:white;
padding:20px;
border-radius:10px;
box-shadow:0 4px 10px rgba(0,0,0,0.2);
margin-bottom:30px;
}

input{
padding:8px;
margin:5px;
border-radius:5px;
border:1px solid #ccc;
}

button{
padding:8px 14px;
background:#28a745;
border:none;
color:white;
border-radius:5px;
cursor:pointer;
}

button:hover{
background:#1f7a34;
}

/* TABLE */

table{
width:100%;
border-collapse:collapse;
background:white;
box-shadow:0 4px 10px rgba(0,0,0,0.2);
}

table th{
background:#66CDAA;
color:white;
padding:10px;
}

table td{
padding:10px;
text-align:center;
}

table tr:nth-child(even){
background:#f2f2f2;
}

a{
color:red;
text-decoration:none;
font-weight:bold;
}

a:hover{
text-decoration:underline;
}

/* BACK BUTTON */

.back{
display:block;
margin-top:20px;
text-align:center;
font-size:18px;
}

</style>

</head>

<body>

<header>
<h1>Farm Produce Management</h1>
</header>

<div class="container">

<!-- PRODUCE IMAGES -->

<div class="gallery">

<img src="assets/images/milk.jpg" alt="Milk">
<img src="assets/images/eggs.jpg" alt="Eggs">
<img src="assets/images/banana.jpg" alt="Banana">
<img src="assets/images/mangoes.jpg" alt="Mangoes">

</div>

<h2>Produce</h2>

<form method="POST">
Produce Name <input type="text" name="produce_name">
Quantity <input type="number" name="quantity">
<button name="add">Add Produce</button>
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
<input type="text" name="produce_name" value="<?php echo $row['produce_name']; ?>">
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