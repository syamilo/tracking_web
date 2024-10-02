<?php
include('database_connection.php');
session_start();

// Only admins can access this page
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}


// Query to get total participants from the activity_record table
$query = "SELECT date, total_activity FROM activity_record";
$result = $conn->query($query);

$activity_result = null;
$date = null;

// Check if a specific date is selected to fetch activity details
if (isset($_GET['date'])) {
    $date = $_GET['date'];
    // Prepared statement for security
    $stmt = $conn->prepare("SELECT name, ic_number FROM customer WHERE booking_date = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $activity_result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Records</title>
    <link rel="stylesheet" href="activity_record.css">
</head>
<body>
    <div class="container">
        <!-- Activity Record List -->
        <h2>Activity Records</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total Participants</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { 
                    // Format the date to d-m-Y
                    $formatted_date = date('d-m-Y', strtotime($row['date']));
                ?>
                    <tr>
                        <td><a href="activity_record.php?date=<?= $row['date'] ?>"><?= $formatted_date ?></a></td>
                        <td><?= $row['total_activity'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php if ($activity_result) { 
            // Format the selected date to d-m-Y
            $formatted_selected_date = date('d-m-Y', strtotime($date));
        ?>
            <h3>Activity Details for <?= htmlspecialchars($formatted_selected_date) ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>IC Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $activity_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['ic_number']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>

        <!-- Back Button -->
        <div class="back-button-wrapper">
            <a href="admin.php" class="back-button">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
