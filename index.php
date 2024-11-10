<?php
// Check if the server is running with HTTPS
if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
    $uri = 'https://';
} else {
    $uri = 'http://';
}
$uri .= $_SERVER['HTTP_HOST'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Company</title>
    <link rel="stylesheet" href="index.css"> <!-- Optional: Add your own CSS for styling -->
</head>
<body>
    <div class="welcome-container">
        <h1>Welcome to Our Company</h1>
        <p>
            We are a leading provider of outdoor adventure experiences, specializing in hiking, jungle trekking, and cave exploring. 
            Our mission is to offer safe and exciting adventures while ensuring that our customers enjoy every moment in nature. 
            Whether you're a seasoned explorer or just starting out, our team is here to guide you on your journey.
        </p>
        <p>
            Your safety is our priority, and we offer real-time GPS tracking to ensure that all participants stay together throughout the activity. 
            Let’s make memories with us in nature!
        </p>
        
        <!-- Hyperlink to Login Page -->
        <a href="/fyp/tracking_web/login.php">Login</a>
        <a href="/fyp/tracking_web/about_us.php">About Us</a>
    </div>
</body>
</html>
