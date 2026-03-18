<?php
include "db.php";

// Fetch totals
$animals = $conn->query("SELECT COUNT(*) AS total FROM animals")->fetch_assoc()['total'];
$crops = $conn->query("SELECT COUNT(*) AS total FROM crops")->fetch_assoc()['total'];
$staff = $conn->query("SELECT COUNT(*) AS total FROM staff")->fetch_assoc()['total'];
$produce = $conn->query("SELECT COUNT(*) AS total FROM produce")->fetch_assoc()['total'];

/* EXTRA ANALYTICS */
$total_all = $animals + $crops + $staff + $produce;

$animals_pct = $total_all ? round(($animals/$total_all)*100,1) : 0;
$crops_pct = $total_all ? round(($crops/$total_all)*100,1) : 0;
$staff_pct = $total_all ? round(($staff/$total_all)*100,1) : 0;
$produce_pct = $total_all ? round(($produce/$total_all)*100,1) : 0;

$categories = [
    "Animals"=>$animals,
    "Crops"=>$crops,
    "Staff"=>$staff,
    "Produce"=>$produce
];
$highest = array_keys($categories, max($categories))[0];

/* CSV DOWNLOAD */
if(isset($_GET['download']) && $_GET['download']=="csv"){
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=farm_report.csv');
    $output = fopen("php://output", "w");
    fputcsv($output, ['Category','Total','Percentage']);
    fputcsv($output, ['Animals',$animals,$animals_pct.'%']);
    fputcsv($output, ['Crops',$crops,$crops_pct.'%']);
    fputcsv($output, ['Staff',$staff,$staff_pct.'%']);
    fputcsv($output, ['Produce',$produce,$produce_pct.'%']);
    fclose($output);
    exit;
}

/* PDF DOWNLOAD */
if(isset($_GET['download']) && $_GET['download']=="pdf"){
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=farm_report.pdf");
    echo "
    FARM REPORT

    Animals: $animals ($animals_pct%)
    Crops: $crops ($crops_pct%)
    Staff: $staff ($staff_pct%)
    Produce: $produce ($produce_pct%)

    Total: $total_all
    Highest Category: $highest
    ";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Farm Reports</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    font-family: Arial;
    margin:0;
    background:#eef2f3;
}

/* HEADER */
header{
    background:linear-gradient(45deg,#28a745,#66CDAA);
    color:white;
    padding:25px;
    text-align:center;
    box-shadow:0 4px 12px rgba(0,0,0,0.2);
}

/* CONTAINER */
.container{
    max-width:1000px;
    margin:auto;
    padding:30px;
}

/* CARDS */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
}

.card{
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 6px 18px rgba(0,0,0,0.2);
    text-align:center;
    border-top:5px solid #ff7b00;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
}

.card h3{
    color:#28a745;
}

.card h2{
    font-size:32px;
}

/* ANALYSIS */
.analysis{
    background:white;
    padding:20px;
    margin-top:20px;
    border-radius:10px;
    box-shadow:0 4px 12px rgba(0,0,0,0.15);
    border-left:6px solid #28a745;
}

.analysis h4{
    color:#28a745;
}

/* INSIGHTS */
.insights{
    margin-top:20px;
    padding:20px;
    background:#fff5e6;
    border-radius:10px;
    border-left:6px solid #ff7b00;
}

.insights h4{
    color:#ff7b00;
}

/* BUTTONS */
.downloads{
    text-align:center;
    margin-top:30px;
}

.btn{
    padding:12px 22px;
    background:#28a745;
    color:white;
    text-decoration:none;
    border-radius:8px;
    margin:10px;
    display:inline-block;
    font-weight:bold;
}

.btn:hover{
    background:#1f7a34;
}

/* BACK LINK */
.back{
    display:block;
    margin-top:30px;
    text-align:center;
    font-weight:bold;
    text-decoration:none;
    color:#ff7b00;
    font-size:16px;
}

.back:hover{
    color:#28a745;
}

/* CHART */
.chart-container{
    background:white;
    padding:25px;
    border-radius:12px;
    margin-top:30px;
    box-shadow:0 6px 18px rgba(0,0,0,0.2);
}
</style>
</head>
<body>

<header>
<h1>Farm Reports Dashboard</h1>
</header>

<div class="container">

<!-- CARDS -->
<div class="cards">
    <div class="card">
        <h3>Animals</h3>
        <h2><?php echo $animals; ?></h2>
        <small><?php echo $animals_pct; ?>%</small>
    </div>
    <div class="card">
        <h3>Crops</h3>
        <h2><?php echo $crops; ?></h2>
        <small><?php echo $crops_pct; ?>%</small>
    </div>
    <div class="card">
        <h3>Staff</h3>
        <h2><?php echo $staff; ?></h2>
        <small><?php echo $staff_pct; ?>%</small>
    </div>
    <div class="card">
        <h3>Produce</h3>
        <h2><?php echo $produce; ?></h2>
        <small><?php echo $produce_pct; ?>%</small>
    </div>
</div>

<!-- ANALYSIS -->
<div class="analysis">
<h4>Analysis Summary</h4>
<p><strong>Total Records:</strong> <?php echo $total_all; ?></p>
<p><strong>Highest Category:</strong> <?php echo $highest; ?></p>
</div>

<!-- INSIGHTS -->
<div class="insights">
<h4>Smart Insights</h4>
<p>📊 The farm is strongest in <strong><?php echo $highest; ?></strong>.</p>
<p>📈 Percentage distribution helps track system balance.</p>
<p>⚡ Improve low-performing areas for better productivity.</p>
</div>

<!-- CHART -->
<div class="chart-container">
<canvas id="farmChart"></canvas>
</div>

<!-- DOWNLOAD -->
<div class="downloads">
<a class="btn" href="?download=csv">Download CSV</a>
<a class="btn" href="?download=pdf">Download PDF</a>
</div>

<!-- BACK -->
<a class="back" href="dashboard.php">⬅ Back to Dashboard</a>

</div>

<script>
const ctx = document.getElementById('farmChart').getContext('2d');

new Chart(ctx,{
    type:'bar',
    data:{
        labels:['Animals','Crops','Staff','Produce'],
        datasets:[{
            label:'Farm Overview',
            data:[<?php echo $animals; ?>,<?php echo $crops; ?>,<?php echo $staff; ?>,<?php echo $produce; ?>],
            backgroundColor:['#28a745','#66CDAA','#ff7b00','#3498db'],
            borderRadius:8
        }]
    },
    options:{
        responsive:true,
        plugins:{
            legend:{display:false}
        },
        scales:{
            y:{beginAtZero:true}
        }
    }
});
</script>

</body>
</html>