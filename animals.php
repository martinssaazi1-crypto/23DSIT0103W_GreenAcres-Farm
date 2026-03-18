<?php
include "auth.php";
?>

<?php
include "db.php";

/* ADD */
if(isset($_POST['add'])){
    $name = $_POST['animal_name'];
    $q = $_POST['quantity'];
    $conn->query("INSERT INTO animals(animal_name,quantity) VALUES('$name','$q')");
}

/* DELETE */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM animals WHERE id=$id");
}

/* UPDATE */
if(isset($_POST['update'])){
    $id = $_POST['id'];
    $name = $_POST['animal_name'];
    $q = $_POST['quantity'];
    $conn->query("UPDATE animals SET animal_name='$name', quantity='$q' WHERE id=$id");
}

$result = $conn->query("SELECT * FROM animals");
?>

<!DOCTYPE html>
<html>
<head>
<title>Farm Animals</title>

<style>

/* ================= GLOBAL ================= */
body{
    font-family:Arial, Helvetica, sans-serif;
    margin:0;
    background: linear-gradient(to bottom, #d4f1f4, #ffffff);
    color:#333;
}

/* ================= HEADER ================= */
header{
    background:#66CDAA;
    color:white;
    text-align:center;
    padding:25px 20px;
    box-shadow:0 4px 12px rgba(0,0,0,0.3);
}

header h1{
    margin:0;
    font-size:28px;
    letter-spacing:1px;
}

/* ================= CONTAINER ================= */
.container{
    width:90%;
    max-width:1000px;
    margin:auto;
    padding:20px;
}

/* ================= ANIMAL IMAGE GALLERY ================= */
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
    transition: transform 0.3s, box-shadow 0.3s;
}

.gallery img:hover{
    transform:scale(1.08);
    box-shadow:0 8px 20px rgba(0,0,0,0.35);
}

/* ================= FORM ================= */
form{
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 6px 15px rgba(0,0,0,0.2);
    margin-bottom:30px;
    display:flex;
    flex-wrap:wrap;
    gap:15px;
    align-items:center;
    transition:0.3s;
}

form:hover{
    box-shadow:0 8px 25px rgba(0,0,0,0.3);
}

form input{
    padding:10px;
    border-radius:5px;
    border:1px solid #ccc;
    flex:1 1 200px;
    transition:0.3s;
}

form input:focus{
    border-color:#28a745;
    outline:none;
}

form button{
    padding:10px 18px;
    background:#28a745;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
    transition:0.3s;
    font-weight:bold;
}

form button:hover{
    background:#1f7a34;
}

/* ================= TABLE ================= */
table{
    width:100%;
    border-collapse:collapse;
    background:white;
    box-shadow:0 6px 18px rgba(0,0,0,0.2);
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

table input{
    width:90%;
    padding:6px;
    border-radius:4px;
    border:1px solid #ccc;
    text-align:center;
}

table button{
    padding:6px 12px;
    background:#28a745;
    color:white;
    border:none;
    border-radius:4px;
    cursor:pointer;
    transition:0.3s;
}

table button:hover{
    background:#1f7a34;
}

a{
    color:red;
    text-decoration:none;
    font-weight:bold;
    transition:0.3s;
}

a:hover{
    text-decoration:underline;
    color:#c0392b;
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
    transition:0.3s;
}

.back:hover{
    color:#28a745;
    transform:scale(1.05);
}

/* ================= RESPONSIVE ================= */
@media(max-width:600px){
    form{
        flex-direction:column;
        align-items:flex-start;
    }
    
    table td input{
        width:100%;
    }
    
    .gallery img{
        width:45%;
        height:auto;
    }
}
</style>

</head>
<body>

<header>
<h1>Farm Animals/Birds</h1>
</header>

<div class="container">

<!-- Animal Images -->
<div class="gallery">
<img src="assets/images/cow.jpg" alt="Cow">
<img src="assets/images/turkey.jpg" alt="Turkey">
<img src="assets/images/goats.jpg" alt="Goats">
<img src="assets/images/pigs.jpg" alt="Pigs">
<img src="assets/images/ducks.jpg" alt="Duckss">
<img src="assets/images/rabbits.jpg" alt="Rabbits">
<img src="assets/images/pigs.jpg" alt="Pigs">
</div>

<h2>Animals</h2>

<form method="POST">
    Animal Name <input type="text" name="animal_name" placeholder="Enter animal/Bird name" required>
    Quantity <input type="number" name="quantity" placeholder="Quantity" required>
    <button name="add">Add Animal/Bird</button>
</form>

<table>
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
<td><input type="text" name="animal_name" value="<?php echo $row['animal_name']; ?>"></td>
<td><input type="number" name="quantity" value="<?php echo $row['quantity']; ?>"></td>
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