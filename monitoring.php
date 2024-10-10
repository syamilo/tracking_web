<?php
include('database_connection.php');
session_start();
if ($_SESSION['role'] != 'staff') {
    header("Location: login.php");
    exit();
}

// Get today's date
$today = date('Y-m-d');

// Query for fetching only today's active customers
$query = "SELECT * FROM customer WHERE booking_date = '$today' AND status = 'present'";
$result = $conn->query($query);

// Handle setting activity duration and save it to session
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['duration'] = $_POST['duration'] * 60 * 60; // Store duration in seconds
}

// Retrieve the duration from session if set
$duration = isset($_SESSION['duration']) ? $_SESSION['duration'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Tracking</title>
    <link rel="stylesheet" href="monitoring.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAILXH4lFnWUq_LdSdDoD5UgSrFBiNIwEE&callback=initMap" async defer></script>
</head>
<body>
    <div class="container">

        <!-- Google Map Integration at the top -->
        <h2>Customer Tracking Information</h2>
        <div id="map"></div>

        <!-- Table for customer details -->
        <table>
            <tr>
                <th>Name</th>
                <th>Colour</th>
                <th>GPS Tracker</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Last Location</th>
                <th>Current Location</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['colour'] ?></td>
                    <td><?= $row['gps_tracker_name'] ?></td>
                    <td><?= $row['latitude'] ?></td>
                    <td><?= $row['longitude'] ?></td>
                    <td><?= $row['last_location'] ?></td>
                    <td><?= $row['current_location'] ?></td>
                </tr>
            <?php } ?>
        </table>

        <!-- Set activity duration -->
        <h3>Set Activity Duration</h3>
        <form method="post" action="">
            <label for="duration">Duration (in hours): </label>
            <input type="number" id="duration" name="duration" min="1" max="24" required>
            <button type="submit">Set Duration</button>
        </form>

        <!-- Countdown timer -->
        <div id="countdown-container" style="display:none;">
            <h3>Time Remaining: <span id="countdown"></span></h3>
        </div>

        <!-- Back Button -->
        <a href="staff.php" class="back-button">Back to Dashboard</a>

    </div>

    <script

        // Initialize the map
        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: {lat: -34.397, lng: 150.644} // Default center, can be changed based on data
            });

            // Replace this part with actual GPS tracker data
            <?php
            // Loop through customer data to create map markers
            $result->data_seek(0); // Reset result pointer
            while ($row = $result->fetch_assoc()) { ?>
                var marker = new google.maps.Marker({
                    position: {lat: <?= $row['latitude'] ?>, lng: <?= $row['longitude'] ?>},
                    map: map,
                    title: '<?= $row['name'] ?>'
                });
            <?php } ?>
        }

        // Countdown Timer
        var duration = <?= $duration ?>; // Get duration in seconds from PHP
        if (duration > 0) {
            document.getElementById('countdown-container').style.display = 'block';
            var countdownElement = document.getElementById('countdown');

            var countdownTimer = setInterval(function() {
                var hours = Math.floor(duration / 3600);
                var minutes = Math.floor((duration % 3600) / 60);
                var seconds = duration % 60;

                countdownElement.textContent = hours + "h " + minutes + "m " + seconds + "s";

                if (duration <= 0) {
                    clearInterval(countdownTimer);
                    countdownElement.textContent = "Time's up!";
                }

                duration--;
            }, 1000);
        }
    </script>
</body>
</html>
