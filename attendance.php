<?php
include('database_connection.php');
session_start();

if ($_SESSION['role'] != 'staff') {
    header("Location: login.php");
    exit();
}

$today = date('Y-m-d');

// Initialize session for ticked customers if not already set
if (!isset($_SESSION['ticked_customers'])) {
    $_SESSION['ticked_customers'] = [];
}

// Initialize success message in session if not set
if (!isset($_SESSION['attendance_message'])) {
    $_SESSION['attendance_message'] = '';
}

// Check if search is performed
$search_ic = isset($_POST['search_ic']) ? $_POST['search_ic'] : '';

// Query to retrieve customer information along with assigned GPS device ID (as tracker name) for today's booking
$query = "
    SELECT customer.*, gps_device.device_ID AS tracker_name
    FROM customer 
    LEFT JOIN device_usage ON customer.id = device_usage.customer_id 
    LEFT JOIN gps_device ON device_usage.device_id = gps_device.id
    WHERE customer.booking_date = '$today'
";

// If an IC number is searched, filter the result by the IC number
if (!empty($search_ic)) {
    $query .= " AND customer.ic_number LIKE '%$search_ic%'";
}

$result = $conn->query($query);

// Load attendance data from the 'attendance' table for today
$attendance_query = "SELECT customer_id, status FROM attendance WHERE attendance_date = '$today'";
$attendance_result = $conn->query($attendance_query);

// Initialize an array to store the ticked customers (present)
$ticked_customers = [];
while ($row = $attendance_result->fetch_assoc()) {
    if ($row['status'] == 'present') {
        $ticked_customers[] = $row['customer_id'];
    }
}

// Handle attendance confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['attendance'])) {
        $_SESSION['ticked_customers'] = $_POST['attendance'];
        $attended_ids = $_SESSION['ticked_customers'];

        // Insert or update attendance records in the 'attendance' table
        foreach ($attended_ids as $customer_id) {
            // Check if the record already exists
            $check_query = "SELECT id FROM attendance WHERE customer_id = '$customer_id' AND attendance_date = '$today'";
            $check_result = $conn->query($check_query);

            if ($check_result->num_rows > 0) {
                // Update the existing record to 'present'
                $update_query = $conn->prepare("UPDATE attendance SET status = 'present' WHERE customer_id = ? AND attendance_date = ?");
                $update_query->bind_param("is", $customer_id, $today);
                $update_query->execute();
            } else {
                // Insert a new record as 'present'
                $insert_query = $conn->prepare("INSERT INTO attendance (customer_id, attendance_date, status) VALUES (?, ?, 'present')");
                $insert_query->bind_param("is", $customer_id, $today);
                $insert_query->execute();
            }
        }

        // Insert or update records for absent customers
        $absent_query = "SELECT id FROM customer WHERE booking_date = '$today' AND id NOT IN (" . implode(',', array_map('intval', $attended_ids)) . ")";
        $absent_result = $conn->query($absent_query);

        while ($row = $absent_result->fetch_assoc()) {
            $absent_customer_id = $row['id'];

            // Check if the record already exists
            $check_absent_query = "SELECT id FROM attendance WHERE customer_id = '$absent_customer_id' AND attendance_date = '$today'";
            $check_absent_result = $conn->query($check_absent_query);

            if ($check_absent_result->num_rows > 0) {
                // Update the existing record to 'absent'
                $update_absent_query = $conn->prepare("UPDATE attendance SET status = 'absent' WHERE customer_id = ? AND attendance_date = ?");
                $update_absent_query->bind_param("is", $absent_customer_id, $today);
                $update_absent_query->execute();
            } else {
                // Insert a new record as 'absent'
                $insert_absent_query = $conn->prepare("INSERT INTO attendance (customer_id, attendance_date, status) VALUES (?, ?, 'absent')");
                $insert_absent_query->bind_param("is", $absent_customer_id, $today);
                $insert_absent_query->execute();
            }
        }

        // Calculate the total number of customers who attended today
        $total_attended = count($_SESSION['ticked_customers']);

        // Update the total_activity in activity_record for the current day
        $update_activity_query = "UPDATE activity_record SET total_activity = $total_attended WHERE date = '$today'";
        $conn->query($update_activity_query);

        // Set a success message
        $_SESSION['attendance_message'] = 'Attendance successfully confirmed. Total customers that attended: ' . $total_attended;
    }
}

// Display message and clear it if the user logs out
if (isset($_GET['logout'])) {
    unset($_SESSION['attendance_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link rel="stylesheet" href="attendance.css">
    <script>
        function confirmAttendance() {
            return confirm('Are you sure you want to confirm the attendance?');
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Customer Attendance</h2>

        <?php if ($_SESSION['attendance_message']): ?>
            <p class="success-message"><?= htmlspecialchars($_SESSION['attendance_message']) ?></p>
        <?php endif; ?>

        <!-- Search form to find customer by IC number -->
        <form method="post" action="attendance.php">
            <input type="text" name="search_ic" placeholder="Enter IC Number" value="<?= htmlspecialchars($search_ic) ?>">
            <button type="submit">Search</button>
        </form>

        <form method="post" action="attendance.php" id="confirmation-form" onsubmit="return confirmAttendance()">
            <table>
                <tr>
                    <th>Name</th>
                    <th>IC Number</th>
                    <th>Colour</th>
                    <th>GPS Tracker</th>
                    <th>Attendance</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['ic_number']) ?></td>
                        <td><?= htmlspecialchars($row['colour']) ?></td>
                        <td><?= htmlspecialchars($row['tracker_name']) ?></td>
                        <td>
                            <input type="checkbox" name="attendance[]" value="<?= htmlspecialchars($row['id']) ?>"
                            <?= in_array($row['id'], $ticked_customers) ? 'checked' : '' ?>>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <button type="submit">Confirm Attendance</button>
        </form>

        <!-- Back to Staff Dashboard Button -->
        <a href="staff.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
