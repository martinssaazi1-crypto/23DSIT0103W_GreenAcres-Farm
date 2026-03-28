<?php
require_once 'db.php';
$order_id = intval($_GET['id']);
$query = $conn->query("SELECT so.*, s.supplier_name, s.phone, p.item_name 
                       FROM supplier_orders so 
                       JOIN suppliers s ON so.supplier_id = s.id 
                       JOIN produce p ON so.produce_id = p.id 
                       WHERE so.id = $order_id");
$data = $query->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delivery Note #<?php echo $order_id; ?></title>
    <style>
        body { font-family: sans-serif; padding: 40px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #27ae60; padding-bottom: 20px; }
        .details { margin-top: 30px; line-height: 1.6; }
        .footer { margin-top: 50px; font-size: 0.8rem; color: #7f8c8d; text-align: center; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>GREEN ACRES FARM</h1>
        <p>Official Delivery Note</p>
    </div>
    <div class="details">
        <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
        <p><strong>Date:</strong> <?php echo date('d M Y, H:i', strtotime($data['order_date'])); ?></p>
        <hr>
        <p><strong>Customer:</strong> <?php echo $data['supplier_name']; ?></p>
        <p><strong>Contact:</strong> <?php echo $data['phone']; ?></p>
        <p><strong>Item Dispatched:</strong> <?php echo $data['item_name']; ?></p>
        <p><strong>Quantity:</strong> <?php echo $data['order_quantity']; ?> Units</p>
    </div>
    <div class="footer">
        <p>Thank you for partnering with Green Acres.</p>
        <button class="no-print" onclick="window.print()">Print Again</button>
    </div>
</body>
</html>