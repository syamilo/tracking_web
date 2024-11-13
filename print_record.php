<?php
include('database_connection.php');
session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Initialize variables
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$record_type = isset($_POST['record_type']) ? $_POST['record_type'] : 'activity';
$title = ''; // Initialize $title variable to avoid 'undefined' error

// Variables to store the results
$records = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($from_date) && !empty($to_date)) {
    switch ($record_type) {
        case 'activity':
            // Query activity_record table
            $query = "SELECT * FROM activity_record WHERE date BETWEEN '$from_date' AND '$to_date'";
            $title = "Activity Records";
            break;
        case 'customer':
            // Query customer table
            $query = "SELECT * FROM customer WHERE booking_date BETWEEN '$from_date' AND '$to_date'";
            $title = "Customer Records";
            break;
        case 'report':
            // Query accident_report table
            $query = "SELECT * FROM accident_report WHERE date BETWEEN '$from_date' AND '$to_date'";
            $title = "Accident Reports";
            break;
    }

    // Execute query and check for errors
    $records = $conn->query($query);
    if (!$records) {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?: 'Website Title') ?></title>
    <link rel="stylesheet" href="print_record.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        form label {
            margin-right: 10px;
        }

        form input[type="date"], form select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
        }

        button[type="submit"], .print-button {
            background-color: blue;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        button[type="submit"]:hover, .print-button:hover {
            background-color: darkblue;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        .print-button {
            display: block;
            margin: 20px auto 0;
            width: 100px;
        }

        .back-button-wrapper {
            margin-top: 20px;
            text-align: center;
        }

        .back-button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        /* Print styling */
        @media print {
            body {
                background: none;
            }

            .container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }

            form, .print-button, .back-button-wrapper {
                display: none;
            }

            table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    
    <div class="container">
        <h1><?= htmlspecialchars($title ?: 'Print record') ?></h1>

        <!-- Date range and record type selection form -->
        <form method="POST" action="print_record.php">
            <div>
                <label for="from_date">From Date:</label>
                <input type="date" id="from_date" name="from_date" value="<?= htmlspecialchars($from_date) ?>" required>
            </div>
            <div>
                <label for="to_date">To Date:</label>
                <input type="date" id="to_date" name="to_date" value="<?= htmlspecialchars($to_date) ?>" required>
            </div>
            <div>
                <label for="record_type">Record Type:</label>
                <select id="record_type" name="record_type">
                    <option value="activity" <?= $record_type == 'activity' ? 'selected' : '' ?>>Activity Records</option>
                    <option value="customer" <?= $record_type == 'customer' ? 'selected' : '' ?>>Customer Records</option>
                    <option value="report" <?= $record_type == 'report' ? 'selected' : '' ?>>Accident Records</option>
                </select>
            </div>
            <button type="submit">Search</button>
        </form>

        <!-- Display Records -->
        <?php if ($records && $records->num_rows > 0): ?>
            <table>
                <?php if ($record_type == 'activity'): ?>
                    <tr>
                        <th>Date</th>
                        <th>Total Activity</th>
                        <th>Activity Details</th>
                    </tr>
                    <?php while ($row = $records->fetch_assoc()): ?>
                        <tr>
                            <!-- Format the date as d/m/y -->
                            <td><?= date('d/m/Y', strtotime($row['date'])) ?></td>
                            <td><?= htmlspecialchars($row['total_activity']) ?></td>
                            <td>
                                <?php
                                // Fetch activity details for each date
                                $activity_date = $row['date'];
                                $detail_query = "
                                    SELECT activity, COUNT(*) AS total_participants
                                    FROM customer
                                    WHERE booking_date = '$activity_date'
                                    GROUP BY activity
                                ";
                                $details_result = $conn->query($detail_query);

                                // Display activity details as a list
                                if ($details_result && $details_result->num_rows > 0) {
                                    echo "<ul>";
                                    while ($detail = $details_result->fetch_assoc()) {
                                        echo "<li>" . htmlspecialchars(ucwords($detail['activity'])) . ": " . htmlspecialchars($detail['total_participants']) . "</li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "No details available";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php elseif ($record_type == 'customer'): ?>
                    <tr>
                        <th>Name</th>
                        <th>IC Number</th>
                        <th>Booking Date</th>
                    </tr>
                    <?php while ($row = $records->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['ic_number']) ?></td>
                            <!-- Format the booking date as d/m/y -->
                            <td><?= date('d/m/Y', strtotime($row['booking_date'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php elseif ($record_type == 'report'): ?>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Date</th>
                    </tr>
                    <?php while ($row = $records->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= htmlspecialchars($row['location']) ?></td>
                            <!-- Format the date as d/m/y -->
                            <td><?= date('d/m/Y', strtotime($row['date'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </table>

            <!-- Print button -->
            <button class="print-button" onclick="window.print()">Print</button>
        <?php else: ?>
            <p>No records found for the selected date range.</p>
        <?php endif; ?>

        <!-- Back button -->
        <div class="back-button-wrapper">
            <a href="admin.php" class="back-button">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
