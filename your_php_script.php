<?php
include 'database_connection.php'; // Include your database connection file

// Debugging: Print the contents of the $_POST array
print_r($_POST);

// Get data from POST request and sanitize it
$name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';

// Check if the name field is not empty
if (!empty($name)) {
    // Insert data into customer table
    $sql = "INSERT INTO customer (name) VALUES ('$name')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Name field is empty.";
}

$conn->close();
?>
