<?php
require_once 'auth.php';
require_once 'db.php';
require_once 'fpdf.php'; // Ensure this file is in your folder!

if (!isset($_GET['type'])) { die("Invalid Request"); }

$type = $_GET['type'];

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'GREEN ACRES FARM MANAGEMENT SYSTEM', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Official Inventory Report - ' . date('Y-m-d'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

if ($type == 'animals') {
    $pdf->Cell(0, 10, 'Livestock Inventory List', 0, 1, 'L');
    $pdf->SetFillColor(39, 174, 96);
    $pdf->SetTextColor(255);
    $pdf->Cell(100, 10, 'Animal Name', 1, 0, 'C', true);
    $pdf->Cell(90, 10, 'Quantity (Heads)', 1, 1, 'C', true);
    
    $pdf->SetTextColor(0);
    $pdf->SetFont('Arial', '', 12);
    $res = $conn->query("SELECT * FROM animals");
    while($row = $res->fetch_assoc()) {
        $pdf->Cell(100, 10, $row['animal_name'], 1);
        $pdf->Cell(90, 10, $row['quantity'], 1, 1);
    }
} 
elseif ($type == 'crops') {
    $pdf->Cell(0, 10, 'Crop Harvest Summary', 0, 1, 'L');
    $pdf->SetFillColor(39, 174, 96);
    $pdf->SetTextColor(255);
    $pdf->Cell(100, 10, 'Crop Variety', 1, 0, 'C', true);
    $pdf->Cell(90, 10, 'Stock (Units)', 1, 1, 'C', true);
    
    $pdf->SetTextColor(0);
    $pdf->SetFont('Arial', '', 12);
    $res = $conn->query("SELECT * FROM crops");
    while($row = $res->fetch_assoc()) {
        $pdf->Cell(100, 10, $row['crop_name'], 1);
        $pdf->Cell(90, 10, $row['quantity'], 1, 1);
    }
}
elseif ($type == 'staff') {
    $pdf->Cell(0, 10, 'Staff Directory', 0, 1, 'L');
    $pdf->SetFillColor(39, 174, 96);
    $pdf->SetTextColor(255);
    $pdf->Cell(100, 10, 'Full Name', 1, 0, 'C', true);
    $pdf->Cell(90, 10, 'Designated Role', 1, 1, 'C', true);
    
    $pdf->SetTextColor(0);
    $pdf->SetFont('Arial', '', 12);
    $res = $conn->query("SELECT * FROM staff");
    while($row = $res->fetch_assoc()) {
        $pdf->Cell(100, 10, $row['name'], 1);
        $pdf->Cell(90, 10, $row['role'], 1, 1);
    }
}

$pdf->Output('D', 'GreenAcres_' . $type . '_Report.pdf');
?>