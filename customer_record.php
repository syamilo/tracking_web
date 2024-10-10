<?php
include('database_connection.php');
session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Check if the user requested to view attendance
$show_attendance = isset($_GET['show_attendance']) ? $_GET['show_attendance'] : false;

// Fetch customers from the database
$query = "SELECT * FROM customer";
$customers_result = $conn->query($query);

$attendance_data = [];

// If attendance is requested, fetch attendance data for each customer
if ($show_attendance) {
    $attendance_query = "SELECT customer_id, status FROM attendance WHERE attendance_date = CURDATE()";
    $attendance_result = $conn->query($attendance_query);

    // Store attendance information in an associative array for easy lookup
    while ($row = $attendance_result->fetch_assoc()) {
        $attendance_data[$row['customer_id']] = $row['status'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Records</title>
    <link rel="stylesheet" href="customer_record.css">
    <style>
        /* Add some basic styles to differentiate attendance status */
        .present { background-color: #d4edda; } /* Green for present */
        .absent { background-color: #f8d7da; }  /* Red for absent */
    </style>
</head>
<body>
    <div class="container">
        <h2>Customer Records</h2>

        <form method="get" action="customer_record.php">
    <div class="button-container">
        <button type="submit" name="show_attendance" value="0" <?= !$show_attendance ? 'disabled' : '' ?>>Register</button>
        <button type="submit" name="show_attendance" value="1" <?= $show_attendance ? 'disabled' : '' ?>>Attendance</button>
    </div>
</form>


        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>IC Number</th>
                    <th>Age</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>Activity</th>
                    <th>Registration Date</th>
                    <?php if ($show_attendance) { ?>
                        <th>Attendance Status</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $customers_result->fetch_assoc()) { 
                    // Format the booking_date to d-m-Y
                    $formatted_date = date('d-m-Y', strtotime($row['booking_date']));

                    // Determine if attendance data exists for the customer
                    $attendance_status = isset($attendance_data[$row['id']]) ? $attendance_data[$row['id']] : 'absent';
                    $row_class = ($attendance_status == 'present') ? 'present' : 'absent';
                ?>
                    <tr class="<?= $show_attendance ? $row_class : '' ?>">
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['ic_number']) ?></td>
                        <td><?= htmlspecialchars($row['age']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= htmlspecialchars($row['phone_number']) ?></td>
                        <td><?= htmlspecialchars($row['activity']) ?></td>
                        <td><?= htmlspecialchars($formatted_date) ?></td>
                        <?php if ($show_attendance) { ?>
                            <td><?= htmlspecialchars($attendance_status == 'present' ? 'Present' : 'Absent') ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="admin.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
