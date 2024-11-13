<?php
include('database_connection.php');
session_start();

// Only admins can access this page
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

date_default_timezone_set('Asia/Kuala_Lumpur'); // Set timezone if needed

$message = ''; // Initialize message

// Get the latest booking date from the customer table
$latest_customer_date_query = "SELECT MAX(booking_date) AS latest_date FROM customer";
$latest_customer_date_result = $conn->query($latest_customer_date_query);
$latest_customer_date_row = $latest_customer_date_result->fetch_assoc();
$latest_customer_date = $latest_customer_date_row['latest_date'];

// Get the latest date in the activity_record table
$latest_activity_date_query = "SELECT MAX(date) AS latest_date FROM activity_record";
$latest_activity_date_result = $conn->query($latest_activity_date_query);
$latest_activity_date_row = $latest_activity_date_result->fetch_assoc();
$latest_activity_date = $latest_activity_date_row['latest_date'];

// Insert new records if the customer table has a more recent booking date than the activity_record table
if ($latest_customer_date > $latest_activity_date) {
    $insert_update_query = "
        INSERT INTO activity_record (id, date, total_activity)
        SELECT NULL, booking_date, COUNT(*) AS total_activity
        FROM customer
        GROUP BY booking_date
        HAVING booking_date NOT IN (SELECT date FROM activity_record)
    ";

    if ($conn->query($insert_update_query) === TRUE) {
        $message = "Activity records updated successfully!";
    } else {
        $message = "Error updating activity records: " . $conn->error;
    }
}

// Retrieve all records from the activity_record table to display total participants per date
$query = "SELECT id, date, total_activity FROM activity_record ORDER BY date DESC";
$result = $conn->query($query);

$activity_result = null;
$date = null;

// If a specific date is selected, fetch detailed activity counts for that date
if (isset($_GET['date'])) {
    $date = $_GET['date'];
    
    // Prepared statement to fetch activity details grouped by activity type for the selected date
    $stmt = $conn->prepare("
        SELECT LOWER(activity) AS activity, COUNT(*) AS total_participants
        FROM customer
        WHERE booking_date = ?
        GROUP BY LOWER(activity)
    ");
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
        <!-- Success/Error Message at the Top and Centered -->
        <?php if ($message) { echo "<div class='message'>$message</div>"; } ?>

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
                    $formatted_date = date('d-m-Y', strtotime($row['date'])); // Format date as d-m-Y
                ?>
                    <tr>
                        <td><a href="activity_record.php?date=<?= $row['date'] ?>"><?= $formatted_date ?></a></td>
                        <td><?= $row['total_activity'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php if ($activity_result && $activity_result->num_rows > 0) { 
            $formatted_selected_date = date('d-m-Y', strtotime($date)); // Format selected date
        ?>
            <h3>Activity Details for <?= htmlspecialchars($formatted_selected_date) ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Activity</th>
                        <th>Total Participants</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $activity_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars(ucwords($row['activity'])) ?></td>
                            <td><?= htmlspecialchars($row['total_participants']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } elseif ($date) { ?>
            <p>No activity records found for <?= htmlspecialchars($formatted_selected_date) ?></p>
        <?php } ?>

        <!-- Back Button -->
        <div class="back-button-wrapper">
            <a href="admin.php" class="back-button">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
