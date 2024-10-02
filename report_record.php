<?php
include('database_connection.php');
session_start();

// Redirect non-admin users to the login page
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all accident reports
$query = "SELECT * FROM accident_report";
$result = $conn->query($query);

// Handle the form submission to update a report
if (isset($_POST['update_report'])) {
    $report_id = $_POST['report_id'];
    $description = $_POST['description'];
    $address = $_POST['address'];

    // Update the report in the database
    $update_query = "UPDATE accident_report SET description='$description', address='$address' WHERE id='$report_id'";
    $conn->query($update_query);
    header("Location: report_record.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Records</title>
    <link rel="stylesheet" href="report_record.css">
</head>
<body>
    <div class="container">
        <h2>Accident Report Records</h2>
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Description</th>
                    <th>Address</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { 
                    // Convert the date to d-m-y format
                    $formatted_date = date('d-m-Y', strtotime($row['date']));
                ?>
                    <tr>
                        <form method="post" action="report_record.php">
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td>
                                <input type="text" name="description" value="<?= htmlspecialchars($row['description']) ?>">
                            </td>
                            <td>
                                <input type="text" name="address" value="<?= htmlspecialchars($row['address']) ?>">
                            </td>
                            <td><?= htmlspecialchars($formatted_date) ?></td>
                            <td>
                                <input type="hidden" name="report_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="update_report" class="edit-button">Edit</button>
                            </td>
                        </form>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Back Button -->
        <div class="back-button-wrapper">
            <a href="admin.php" class="back-button">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
