<?php
session_start();
if ($_SESSION['role'] != 'staff') {
    header("Location: /fyp/tracking_web/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="setaff.css">
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
            <a href="registration.php" class="dashboard-item">
                <img src="register_icon.png" alt="Registration Icon">
                <p>Registration</p>
            </a>
            <a href="attend.php" class="dashboard-item">
                <img src="icon_attendances.png" alt="Attendance Icon">
                <p>Attendance</p>
            </a>
            <a href="monitoring.php" class="dashboard-item">
                <img src="icon_monitor.png" alt="Monitoring Icon">
                <p>Monitoring</p>
            </a>
            <a href="report.php" class="dashboard-item">
                <img src="report_icon.png" alt="Report Icon">
                <p>Report</p>
            </a>
        </div>
    </div>
</body>
</html>
