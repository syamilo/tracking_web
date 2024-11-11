<?php
include('database_connection.php');

// SQL query to get the latest coordinates for Tracker C
$sql = "SELECT latitude, longitude, timestamp FROM gps_device WHERE device_ID = 'Tracker C' ORDER BY timestamp DESC LIMIT 1";
$result = $conn->query($sql);

$response = array();

// Check if a result was found
if ($result->num_rows > 0) {
    // Fetch the latest row for Tracker C
    $row = $result->fetch_assoc();
    $response["latitude"] = (float) $row["latitude"];
    $response["longitude"] = (float) $row["longitude"];
    $response["timestamp"] = $row["timestamp"];
    $response["status"] = "success";
} else {
    // No data found for Tracker C
    $response["status"] = "error";
    $response["message"] = "No data found for Tracker C";
}

// Close the database connection
$conn->close();

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>