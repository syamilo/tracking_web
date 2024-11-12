<?php
include('database_connection.php');
session_start();
if ($_SESSION['role'] != 'staff') {
    header("Location: login.php");
    exit();
}
date_default_timezone_set('Asia/Kuala_Lumpur');

// Initialize success message and report details
$success_message = '';
$report_details = '';

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
            // Set success message and prepare report details for printing
            $success_message = 'Accident report submitted successfully.';
            $report_details = "
                <h3>Accident Report</h3>
                <p><strong>Date:</strong> $date</p>
                <p><strong>Customer:</strong> $customer_name</p>
                <p><strong>Location:</strong> $location</p>
                <p><strong>Description:</strong> $description</p>
            ";
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
    <script>
        function printReport() {
            const printContents = document.getElementById('report-details').innerHTML;
            const originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload(); // Reload to restore the page
        }
    </script>
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
                
                // Query to get customers who attended today
                $customers = $conn->query("
                    SELECT customer.id, customer.name 
                    FROM customer 
                    JOIN attendance ON customer.id = attendance.customer_id 
                    WHERE DATE(customer.booking_date) = '$today' 
                      AND attendance.attendance_date = '$today' 
                      AND attendance.status = 'present'
                ");
                
                // Populate the dropdown with customers who attended today
                if ($customers->num_rows > 0) {
                    while ($row = $customers->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                } else {
                    echo "<option value='' disabled>No customers attended today</option>";
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

        <!-- Print Report Section -->
        <?php if (!empty($report_details)): ?>
            <div id="report-details" style="margin-top: 20px;">
                <?= $report_details ?>
                <button onclick="printReport()">Print Report</button>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
