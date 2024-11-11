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

// Handle form submission for assigning guider
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_guider'])) {
    $booking_date = $_POST['booking_date'];
    $guider_id = $_POST['guider'];

    // Insert guider assignment into the guider table
    $stmt = $conn->prepare("INSERT INTO guider (user_id, booking_date) VALUES (?, ?)");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("is", $guider_id, $booking_date);

    if ($stmt->execute()) {
        $message = "Guider assigned successfully for " . htmlspecialchars($booking_date) . "!";
    } else {
        $message = "Error assigning guider: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
}

// Determine which table to show
$view = isset($_POST['view']) ? $_POST['view'] : 'not_assigned';  // Default to 'not_assigned'

// Query to fetch booking dates that already have a guider assigned
$assigned_sql = "SELECT booking_date, users.name AS guider_name 
                 FROM guider 
                 JOIN users ON guider.user_id = users.id";
$assigned_result = $conn->query($assigned_sql);

// Get assigned dates in an array
$assigned_dates = [];
if ($assigned_result->num_rows > 0) {
    while ($row = $assigned_result->fetch_assoc()) {
        $assigned_dates[$row['booking_date']] = $row['guider_name'];
    }
}

// Queries based on the view
if ($view == 'not_assigned') {
    // Query to fetch booking dates that do not have a guider assigned
    $sql = "SELECT booking_date, COUNT(*) AS total_customers 
            FROM customer 
            WHERE booking_date NOT IN (SELECT booking_date FROM guider) 
            GROUP BY booking_date";
} else {
    // Query to fetch booking dates that already have a guider assigned
    $sql = "SELECT booking_date, COUNT(*) AS total_customers 
            FROM customer 
            WHERE booking_date IN (SELECT booking_date FROM guider) 
            GROUP BY booking_date";
}

$result = $conn->query($sql);
if ($result === false) {
    die('Query failed: ' . htmlspecialchars($conn->error));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Guider</title>
    <link rel="stylesheet" type="text/css" href="guider.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">

        <!-- Success/Error Message at the Top and Centered -->
        <?php if ($message) { echo "<div class='message'>$message</div>"; } ?>

        <!-- Table Title Based on Selected View -->
        <h2><?= $view == 'not_assigned' ? 'Booking Dates Without Assigned Guiders:' : 'Booking Dates With Assigned Guiders:' ?></h2>
        
        <!-- Buttons to toggle between views -->
        <form method="POST" action="">
            <button type="submit" name="view" value="not_assigned" <?= ($view == 'not_assigned') ? 'disabled' : ''; ?>>
                Show Unassigned Guider Dates
            </button>
            <button type="submit" name="view" value="assigned" <?= ($view == 'assigned') ? 'disabled' : ''; ?>>
                Show Assigned Guider Dates
            </button>
        </form>

        <table border="1">
            <tr>
                <th>Booking Date</th>
                <th>Total Customers</th>
                <th>Guider Status</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $date = $row['booking_date'];
                    $formatted_date = (new DateTime($date))->format('d/m/Y');
                    $total_customers = $row['total_customers'];

                    echo "<tr><td>" . $formatted_date . "</td><td>" . $total_customers . "</td>";

                    if ($view == 'assigned') {
                        // If showing assigned guiders, display the guider name
                        echo "<td>Assigned to " . htmlspecialchars($assigned_dates[$date]) . "</td></tr>";
                    } else {
                        // If showing unassigned dates, show 'Not Assigned'
                        echo "<td>Not Assigned</td></tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='3'>No booking dates found.</td></tr>";
            }
            ?>
        </table>

        <!-- Form to assign guider (only show if on unassigned view) -->
        <?php if ($view == 'not_assigned') { ?>
            <h2>Select a Booking Date to Assign a Guider:</h2>
            <?php if ($result->num_rows > 0) { ?>
                <form action="" method="POST">
                    <label for="booking_date">Booking Date:</label>
                    <select name="booking_date" required>
                        <?php
                        // Output each unassigned booking date as an option
                        $result->data_seek(0);  // Reset pointer to fetch data again
                        while ($row = $result->fetch_assoc()) {
                            $date = $row['booking_date'];
                            $formatted_date = (new DateTime($date))->format('d/m/Y');
                            echo "<option value='" . htmlspecialchars($row['booking_date']) . "'>" . htmlspecialchars($formatted_date) . "</option>";
                        }
                        ?>
                    </select>

                    <!-- Query to fetch available guiders from users table -->
                    <label for="guider">Choose Guider:</label>
                    <select name="guider" required>
                        <?php
                        $guider_sql = "SELECT id, name FROM users WHERE role = 'guider'";
                        $guider_result = $conn->query($guider_sql);

                        if ($guider_result->num_rows > 0) {
                            // Output each guider as an option
                            while ($guider_row = $guider_result->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($guider_row['id']) . "'>" . htmlspecialchars($guider_row['name']) . "</option>";
                            }
                        } else {
                            echo "<option value=''>No available guiders</option>";
                        }
                        ?>
                    </select>

                    <input type="submit" name="assign_guider" value="Assign Guider">
                </form>
            <?php } else {
                echo "No unassigned booking dates available.";
            } ?>
        <?php } ?>

        <a href="admin.php" class="back-button">Back to Dashboard</a>

    </div>

<?php
$conn->close(); // Close the connection
?>
</body>
</html>
