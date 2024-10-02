<?php
include('database_connection.php');
session_start();

// Ensure the user is logged in and is staff
if ($_SESSION['role'] != 'staff') {
    header("Location: login.php");
    exit();
}

$success_message = "";  // Initialize success message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $ic_number = $_POST['ic_number'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $booking_date = $_POST['booking_date'];
    $color = $_POST['color'];
    $activity = $_POST['activity'];
    $attended = 0; // Assuming default value for attended is 0 (not attended)

    // Insert into customer table
    $customer_query = "INSERT INTO customer (name, age, ic_number, address, phone_number, booking_date, colour, activity, attended) 
                       VALUES ('$name', '$age', '$ic_number', '$address', '$phone_number', '$booking_date', '$color', '$activity', '$attended')";
    
    if ($conn->query($customer_query)) {
        // Check if an activity record already exists for this booking date
        $check_activity_query = "SELECT * FROM activity_record WHERE date = '$booking_date'";
        $activity_result = $conn->query($check_activity_query);

        if ($activity_result->num_rows > 0) {
            // If an activity record exists, update the total_activity count
            $update_activity_query = "UPDATE activity_record SET total_activity = total_activity + 1 WHERE date = '$booking_date'";
            $conn->query($update_activity_query);
        } else {
            // If no activity record exists, insert a new one with total_activity = 1
            $insert_activity_query = "INSERT INTO activity_record (date, total_activity) VALUES ('$booking_date', 1)";
            $conn->query($insert_activity_query);
        }

        // Set success message
        $success_message = "Customer successfully registered and activity recorded!";
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
</head>
<body>
    <div class="form-container">
        <h2>Register Customer</h2>

        <!-- Show success message if registration is successful -->
        <?php if (!empty($success_message)): ?>
            <p class='success-message'><?php echo $success_message; ?></p>
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
                <input type="date" name="booking_date" id="booking_date" required>
            </div>
            <div class="form-group">
                <label for="color">Choose a Color</label>
                <select name="color" id="color" required>
                    <option value="red">Red</option>
                    <option value="blue">Blue</option>
                    <option value="green">Green</option>
                </select>
            </div>
            <div class="form-group">
                <label for="activity">Activity</label>
                <input type="text" name="activity" id="activity" required>
            </div>
            <button type="submit" class="submit-button">Register</button>
        </form>
        <a href="staff.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
