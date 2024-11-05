<?php
include('database_connection.php');
session_start();

if ($_SESSION['role'] != 'staff') {
    header("Location: login.php");
    exit();
}

$success_message = "";

// Pre-set booking date if available from GET or POST
$booking_date = $_GET['booking_date'] ?? ($_POST['booking_date'] ?? '');

// Set timezone to ensure consistent date comparison
date_default_timezone_set('Asia/Kuala_Lumpur');
$today = date('Y-m-d');

// Step 1: Clear GPS device assignments for past bookings in the gps_device table
$clear_query = "
    UPDATE gps_device 
    SET customer_id = NULL 
    WHERE customer_id IN (
        SELECT id FROM customer 
        WHERE booking_date < '$today'
    )
";
$conn->query($clear_query);

// Step 2: Fetch available GPS devices for the booking date
$available_devices = [];
if ($booking_date) {
    $available_devices_query = "
        SELECT d.id, d.device_ID 
        FROM gps_device d
        LEFT JOIN device_usage du ON d.id = du.device_id AND du.booking_date = '$booking_date'
        WHERE du.device_id IS NULL
    ";
    $available_devices_result = $conn->query($available_devices_query);
    while ($row = $available_devices_result->fetch_assoc()) {
        $available_devices[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'], $_POST['gps_device_id'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $age = $_POST['age'];
    $ic_number = $conn->real_escape_string($_POST['ic_number']);
    $address = $conn->real_escape_string($_POST['address']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $booking_date = $_POST['booking_date'];
    $color = $_POST['color'];
    $activity = $conn->real_escape_string($_POST['activity']);
    $gps_device_id = $_POST['gps_device_id'];

    // Step 3: Insert customer details into the customer table
    $customer_query = "INSERT INTO customer (name, age, ic_number, address, phone_number, booking_date, colour, activity) 
                       VALUES ('$name', '$age', '$ic_number', '$address', '$phone_number', '$booking_date', '$color', '$activity')";

    if ($conn->query($customer_query)) {
        // Get the last inserted customer_id
        $customer_id = $conn->insert_id;

        // Check if device is already booked in device_usage for the selected booking date
        $device_in_use_query = "
            SELECT COUNT(*) AS count FROM device_usage 
            WHERE device_id = $gps_device_id AND booking_date = '$booking_date'
        ";
        $device_in_use_result = $conn->query($device_in_use_query);
        $device_in_use = $device_in_use_result->fetch_assoc()['count'];

        if ($device_in_use == 0) {
            // Insert device usage record
            $device_usage_query = "INSERT INTO device_usage (device_id, booking_date, customer_id) 
                                   VALUES ('$gps_device_id', '$booking_date', '$customer_id')";
            $conn->query($device_usage_query);

            $success_message = "Customer successfully registered and GPS device reserved!";
        } else {
            $success_message = "Error: The selected GPS device is already booked for this date.";
        }
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration</title>
    <link rel="stylesheet" href="registration.css">
    <script>
        function fetchAvailableDevices() {
            const bookingDate = document.getElementById('booking_date').value;
            window.location.href = `registration.php?booking_date=${bookingDate}`;
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Register Customer</h2>

        <?php if (!empty($success_message)): ?>
            <p class='success-message'><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>

        <form method="post" action="registration.php">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" name="age" id="age" required>
            </div>
            <div class="form-group">
                <label for="ic_number">IC Number</label>
                <input type="text" name="ic_number" id="ic_number" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" name="address" id="address" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" required>
            </div>
            <div class="form-group">
                <label for="booking_date">Booking Date</label>
                <input type="date" name="booking_date" id="booking_date" value="<?= htmlspecialchars($booking_date) ?>" required onchange="fetchAvailableDevices()">
            </div>
            <div class="form-group">
                <label for="color">Choose a Color</label>
                <select name="color" id="color" required>
                    <option value="Red">Red</option>
                    <option value="Blue">Blue</option>
                    <option value="Green">Green</option>
                    <option value="Yellow">Yellow</option>
                    <option value="Orange">Orange</option>
                    <option value="Magenta">Magenta</option>
                    <option value="Pink">Pink</option>
                </select>
            </div>
            <div class="form-group">
                <label for="activity">Activity</label>
                <input type="text" name="activity" id="activity" required>
            </div>

            <div class="form-group">
                <label for="gps_device_id">Choose GPS Device</label>
                <select name="gps_device_id" id="gps_device_id" required>
                    <option value="">Select a GPS Device</option>
                    <?php foreach ($available_devices as $device): ?>
                        <option value="<?= $device['id'] ?>"><?= htmlspecialchars($device['device_ID']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="submit-button">Register</button>
        </form>
        <a href="staff.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
