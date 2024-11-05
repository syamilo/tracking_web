<?php
include('database_connection.php');

// Ensure data is received from POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get JSON data from POST
    $data = json_decode(file_get_contents("php://input"), true);
    $latitude = $data['latitude'];
    $longitude = $data['longitude'];
    $device_ID = $data['device_ID'];

    // Update the gps_device table with the received data
    $query = $conn->prepare("UPDATE gps_device SET latitude = ?, longitude = ?, timestamp = CURRENT_TIMESTAMP WHERE device_ID = ?");
    $query->bind_param("dds", $latitude, $longitude, $device_ID);

    if ($query->execute()) {
        echo "GPS data updated successfully for $device_ID";
    } else {
        echo "Failed to update GPS data: " . $conn->error;
    }
}
?>
