<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: /fyp/tracking_web/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <div class="user-info">
            <p>Welcome, <?= $_SESSION['username'] ?></p>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </header>

    <div class="dashboard-container">
        <h1>Welcome Admin</h1>
        <nav>
                <a href="activity_record.php">Activity Record</a>
                <a href="customer_record.php">Customer Record</a>
                <a href="staff_management.php">Staff Management</a>
                <a href="report_record.php">Report Record</a>
                <a href="print_record.php">Print Record</a>
        </nav>
    </div>
</body>
</html>
