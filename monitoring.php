<?php
include('database_connection.php');
session_start();

if ($_SESSION['role'] != 'staff') {
    header("Location: login.php");
    exit();
}

// Set timezone
date_default_timezone_set('Asia/Kuala_Lumpur');
$today = date('Y-m-d');

// Clear outdated GPS device assignments
$clear_query = "
    UPDATE gps_device 
    SET customer_id = NULL 
    WHERE customer_id IS NOT NULL
";
if (!$conn->query($clear_query)) {
    die("Clear query failed: " . $conn->error);
}

// Assign GPS devices to customers for today's bookings
$assign_query = "
    UPDATE gps_device AS d
    JOIN device_usage AS du ON d.id = du.device_id
    SET d.customer_id = du.customer_id
    WHERE du.booking_date = '$today'
";
if (!$conn->query($assign_query)) {
    die("Assign query failed: " . $conn->error);
}

// Fetch customers with bookings for today and their GPS data
$query = "
    SELECT customer.*, 
           gps_device.latitude, 
           gps_device.longitude, 
           gps_device.device_ID, 
           gps_device.timestamp
    FROM customer
    LEFT JOIN gps_device ON customer.id = gps_device.customer_id 
    LEFT JOIN (
        SELECT customer_id, MAX(timestamp) AS latest_timestamp
        FROM gps_device
        GROUP BY customer_id
    ) AS latest_gps ON gps_device.customer_id = latest_gps.customer_id 
                      AND gps_device.timestamp = latest_gps.latest_timestamp
    WHERE customer.booking_date = '$today'
    ORDER BY customer.booking_date DESC
";
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}

$customers = [];
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

// Duration and Countdown logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['duration'] = $_POST['duration'] * 60 * 60; 
    $_SESSION['start_time'] = time(); 
}

$duration = isset($_SESSION['duration']) ? $_SESSION['duration'] : 0;
$start_time = isset($_SESSION['start_time']) ? $_SESSION['start_time'] : 0;

$elapsed_time = time() - $start_time;
$remaining_time = $duration - $elapsed_time;
if ($remaining_time < 0) {
    $remaining_time = 0; 
}
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
        <h2>Customer Tracking Information</h2>

        <label for="customer-select">Choose a customer:</label>
        <select id="customer-select" onchange="updateMap()">
            <option value="">Select a customer</option>
            <?php foreach ($customers as $customer) { ?>
                <option value="<?= $customer['id'] ?>" 
                        data-lat="<?= $customer['latitude'] ?>" 
                        data-lng="<?= $customer['longitude'] ?>" 
                        data-color="<?= $customer['colour'] ?>">
                    <?= $customer['name'] ?>
                </option>
            <?php } ?>
        </select>

        <button id="show-all" onclick="showAllLocations()">Show All Locations</button>

        <div id="map" style="height: 500px;"></div>

        <table id="customer-table">
            <tr>
                <th>Name</th>
                <th>Color</th>
                <th>GPS Tracker</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Timestamp</th>
                <th>View Location</th>
            </tr>
            <?php foreach ($customers as $row) { ?>
                <tr class="customer-row" data-id="<?= $row['id'] ?>" data-color="<?= $row['colour'] ?>">
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['colour'] ?></td>
                    <td><?= $row['device_ID'] ?></td>
                    <td><?= $row['latitude'] ?></td>
                    <td><?= $row['longitude'] ?></td>
                    <td><?= $row['timestamp'] ?></td>
                    <td>
                        <button class="location-button" data-lat="<?= $row['latitude'] ?>" data-lng="<?= $row['longitude'] ?>">View</button>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <h3>Set Activity Duration</h3>
        <form method="post" action="">
            <label for="duration">Duration (in hours): </label>
            <input type="number" id="duration" name="duration" min="1" max="24" required>
            <button type="submit">Set Duration</button>
            <div id="countdown-container" style="margin-left: 20px;">
                <h3>Time Remaining: <span id="countdown"></span></h3>
            </div>
        </form>

        <a href="staff.php" class="back-button">Back to Dashboard</a>
    </div>

    <script>
    var map;
    var markers = {};

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: {lat: 6.434, lng: 100.341}
        });
        displayAllMarkers();
    }

    function displayAllMarkers() {
        <?php foreach ($customers as $customer) { ?>
            var lat = parseFloat("<?= $customer['latitude'] ?>");
            var lng = parseFloat("<?= $customer['longitude'] ?>");
            var customerName = "<?= $customer['name'] ?>";
            var customerColor = "<?= $customer['colour'] ?>";

            var marker = new google.maps.Marker({
                position: {lat: lat, lng: lng},
                map: map,
                title: customerName,
                icon: createMarkerIcon(customerColor)
            });
            markers["<?= $customer['id'] ?>"] = marker;
        <?php } ?>
    }

    function updateMap() {
        var select = document.getElementById('customer-select');
        var selectedCustomerId = select.value;

        document.querySelectorAll('.customer-row').forEach(function(row) {
            row.style.display = selectedCustomerId === "" || row.getAttribute('data-id') === selectedCustomerId ? "" : "none";
        });

        if (selectedCustomerId) {
            var lat = parseFloat(select.options[select.selectedIndex].getAttribute('data-lat'));
            var lng = parseFloat(select.options[select.selectedIndex].getAttribute('data-lng'));
            map.setCenter({lat: lat, lng: lng});
        } else {
            map.setCenter({lat: 6.434, lng: 100.341});
        }
    }

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('location-button')) {
            var lat = parseFloat(event.target.getAttribute('data-lat'));
            var lng = parseFloat(event.target.getAttribute('data-lng'));
            var color = event.target.closest('tr').getAttribute('data-color');

            map.setCenter({lat: lat, lng: lng});

            for (var key in markers) {
                markers[key].setMap(null);
            }
            markers = {};

            var marker = new google.maps.Marker({
                position: {lat: lat, lng: lng},
                map: map,
                title: "Historical Location",
                icon: createMarkerIcon(color)
            });
            markers["historical"] = marker;
        }
    });

    function createMarkerIcon(color) {
        return {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 8,
            fillColor: color,
            fillOpacity: 1,
            strokeColor: 'white',
            strokeWeight: 2
        };
    }

    function showAllLocations() {
        document.querySelectorAll('.customer-row').forEach(function(row) {
            row.style.display = "";
        });
        map.setCenter({lat: 6.434, lng: 100.341});
        displayAllMarkers();
    }

    // Countdown timer that updates every second
    function startCountdown(duration) {
        var countdownDisplay = document.getElementById('countdown');
        var remainingTime = duration;

        setInterval(function() {
            if (remainingTime > 0) {
                var hours = Math.floor(remainingTime / 3600);
                var minutes = Math.floor((remainingTime % 3600) / 60);
                var seconds = remainingTime % 60;

                countdownDisplay.textContent = 
                    (hours > 0 ? hours + "h " : "") +
                    (minutes > 0 ? minutes + "m " : "") +
                    (seconds > 0 ? seconds + "s" : "");
                remainingTime--;
            } else {
                countdownDisplay.textContent = "Time is up!";
            }
        }, 1000);
    }

    startCountdown(<?= $remaining_time ?>);
    </script>
</body>
</html>
