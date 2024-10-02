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

// Query to retrieve customer information for today's booking
$query = "SELECT * FROM customer WHERE booking_date='$today'";

// If an IC number is searched, filter the result by the IC number
if (!empty($search_ic)) {
    $query .= " AND ic_number LIKE '%$search_ic%'";
}

$result = $conn->query($query);

// Handle attendance confirmation
$attendance_confirmed = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['attendance'])) {
        $_SESSION['ticked_customers'] = $_POST['attendance'];
        $attended_ids = $_SESSION['ticked_customers'];

        // Delete customers who were not ticked (absent)
        $delete_query = "DELETE FROM customer WHERE booking_date='$today' AND id NOT IN (" . implode(',', array_map('intval', $attended_ids)) . ")";
        $conn->query($delete_query);

        // Calculate the total number of customers who attended today
        $total_attended = count($_SESSION['ticked_customers']);

        // Update the total_activity in activity_record for the current day
        $update_activity_query = "UPDATE activity_record SET total_activity = $total_attended WHERE date = '$today'";
        $conn->query($update_activity_query);

        // Set a success message
        $_SESSION['attendance_message'] = 'Attendance successfully confirmed. Total customers that attended: ' . $total_attended;
        $attendance_confirmed = true;
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
            return confirm('Are you sure you want to confirm the attendance? This action will remove customers who are absent.');
        }

        function saveTickedCustomers() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const ticked = Array.from(checkboxes).filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
            sessionStorage.setItem('ticked_customers', JSON.stringify(ticked));
        }

        function loadTickedCustomers() {
            const ticked = JSON.parse(sessionStorage.getItem('ticked_customers') || '[]');
            ticked.forEach(id => {
                const checkbox = document.querySelector(`input[type="checkbox"][value="${id}"]`);
                if (checkbox) checkbox.checked = true;
            });
        }

        window.onload = loadTickedCustomers;
    </script>
</head>
<body>
    <div class="container">
        <h2>Customer Attendance</h2>

        <?php if ($_SESSION['attendance_message']): ?>
            <p class="success-message"><?= htmlspecialchars($_SESSION['attendance_message']) ?></p>
        <?php endif; ?>

        <!-- Search form to find customer by IC number -->
        <form method="post" action="attendance.php" onsubmit="saveTickedCustomers()">
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
                        <td>GPS Tracker</td>
                        <td>
                            <input type="checkbox" name="attendance[]" value="<?= htmlspecialchars($row['id']) ?>" 
                            <?= in_array($row['id'], $_SESSION['ticked_customers']) ? 'checked' : '' ?>>
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
