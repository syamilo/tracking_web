<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="addmin.css">
</head>
<body>
    <!-- Header with user info and logout button -->
    <header>
        <div class="user-info">
            <p>Welcome, <?= $_SESSION['username'] ?></p>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </header>

    <!-- Main container for the dashboard with icons -->
    <div class="dashboard-container">
    <h1>Staff Menu</h1>
    <div class="dashboard-links">
        <a href="activity_record.php" class="dashboard-item">
            <img src="act_rec.png" alt="Activity Record Icon">
            <p>Activity Record</p>
        </a>
        <a href="customer_record.php" class="dashboard-item">
            <img src="cus_rec.png" alt="Customer Record Icon">
            <p>Customer Record</p>
        </a>
        <a href="staff_management.php" class="dashboard-item">
            <img src="setaf_icon.png" alt="Staff Management Icon">
            <p>Staff Management</p>
        </a>
        <a href="guider.php" class="dashboard-item">
            <img src="report_icon.png" alt="Booking Date and Guider Icon">
            <p>Booking Date and Guider</p>
        </a>
        <a href="report_record.php" class="dashboard-item">
            <img src="reprec_icon.png" alt="Report Record Icon">
            <p>Report Record</p>
        </a>
        <a href="print_record.php" class="dashboard-item">
            <img src="icon_monitor.png" alt="Print Record Icon">
            <p>Print Record</p>
        </a>
    </div>
</div>

</body>
</html>
