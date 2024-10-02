<?php
include('database_connection.php');
session_start();
if ($_SESSION['role'] != 'staff') {
    header("Location: login.php");
    exit();
}

// Initialize success message
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $date = date('Y-m-d'); // Store as Y-m-d

    // Fetch the customer's name based on customer_id
    $customer_query = "SELECT name FROM customer WHERE id = '$customer_id'";
    $customer_result = $conn->query($customer_query);

    if ($customer_result->num_rows > 0) {
        $customer_row = $customer_result->fetch_assoc();
        $customer_name = $customer_row['name'];

        // Insert accident report into the database
        $query = "INSERT INTO accident_report (customer_id, name, description, address, date) 
                  VALUES ('$customer_id', '$customer_name', '$description', '$location', '$date')";

        if ($conn->query($query) === TRUE) {
            // Set success message
            $success_message = 'Accident report submitted successfully.';
        } else {
            // Set error message
            $success_message = 'Error: ' . $conn->error;
        }
    } else {
        // If customer not found, set an error message
        $success_message = 'Error: Customer not found.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Accident Report</title>
    <link rel="stylesheet" href="report.css">
</head>
<body>
    <div class="form-container">
        <h2>Submit Accident Report</h2>
        
        <?php if ($success_message): ?>
            <p class="success-message"><?= htmlspecialchars($success_message) ?></p>
        <?php endif; ?>
        
        <form method="post" action="report.php">
            <label for="customer_id">Select Customer</label>
            <select name="customer_id" id="customer_id" required>
                <option value="" disabled selected>Select a customer</option>
                <?php
                // Get today's date
                $today = date('Y-m-d');
                
                // Query to get customers registered today
                $customers = $conn->query("SELECT id, name FROM customer WHERE DATE(booking_date) = '$today'");
                
                // Populate the dropdown with today's customers
                if ($customers->num_rows > 0) {
                    while ($row = $customers->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                } else {
                    echo "<option value='' disabled>No customers registered today</option>";
                }
                ?>
            </select>

            <label for="description">Accident Description</label>
            <textarea name="description" id="description" placeholder="Accident description" required></textarea>

            <label for="location">Accident Location</label>
            <input type="text" name="location" id="location" placeholder="Location" required>

            <button type="submit">Submit Report</button>
            <a href="staff.php" class="back-button">Back to Staff Dashboard</a>
        </form>
    </div>
</body>
</html>
