<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

// Google Sheets API setup
$client = new Client();
$client->setApplicationName('Google Sheets API PHP');
$client->setScopes([Sheets::SPREADSHEETS_READONLY]);
$client->setAuthConfig('path/to/your/json/keyfile.json');
$service = new Sheets($client);

// ID of the Google Sheet
$spreadsheetId = '18yV_aXpSoPeabsMQR3GiMRBuJj5eGt7sxM-VOShyD2Q/edit?resourcekey=&gid=500428367#gid=500428367';
$range = 'Sheet1!A1:C'; // Adjust the range as needed

// Fetch data from Google Sheets
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

// MySQL connection setup
$servername = "localhost";
$username = "root";
$password = "Milo123_";
$dbname = "tracking_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert data into MySQL
foreach ($values as $row) {
    $sql = "INSERT INTO customer (column1, column2, column3) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $row[0], $row[1], $row[2]);
    $stmt->execute();
}

$stmt->close();
$conn->close();
?>
