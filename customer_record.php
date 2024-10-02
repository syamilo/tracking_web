<?php
include('database_connection.php');
session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch customers from the database
$query = "SELECT * FROM customer";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Records</title>
    <link rel="stylesheet" href="customer_record.css">
</head>
<body>
    <div class="container">
        <h2>Customer Records</h2>

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
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { 
                    // Format the booking_date to d-m-Y
                    $formatted_date = date('d-m-Y', strtotime($row['booking_date']));
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['ic_number']) ?></td>
                        <td><?= htmlspecialchars($row['age']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= htmlspecialchars($row['phone_number']) ?></td>
                        <td><?= htmlspecialchars($row['activity']) ?></td>
                        <td><?= htmlspecialchars($formatted_date) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="admin.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
