<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - SMART TRACKING TOOLS FOR RECREATIONAL CLIMBERS</title>
   
    <style>
       
        body{
            font-family: Arial, sans-serif;
            background-image: url('img/wlppr.img');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;

        }
        .team .member img {
            max-width: 100px; /* Limit the maximum width of the photos */
            height: auto; /* Maintain aspect ratio */
        }
        
        .back-btn {
            background-color: #007bff; /* Blue background color */
            color: white; /* White text color */
            padding: 10px 20px; /* Add padding as needed */
            text-decoration: none; /* Remove underline */
            border-radius: 5px; /* Add border radius */
            display: left; /* Make it inline block */
            width: 60px;
        }

        .back-btn:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        .divider {
            border-top: 1px solid #ccc;
            margin: 20px 0;
        }

        /* Arrange team information horizontally */
        .team {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap; /* Allow wrapping to the next line */
        }

        /* Style individual team member */
        .team-member {
            flex-basis: 30%; /* Adjust width as needed */
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin: 0 10px;
        }
        .container {
            width: 80%; /* Adjust as needed */
            margin: 0 auto;
            padding: 20px;
        }
        footer{
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
            height: 70px;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
<header>
       
    <div class="back-btn"><a href="index.php" class="back-btn">Back</a></div>
    <div class="container"><h1>SMART TRACKING TOOLS FOR RECREATIONAL CLIMBERS</h1></div>
    </header>
    <section class="about">
        <div class="container">
            <h2>About Us</h2>
            <p>
            We specialize in providing GPS-based tracking solutions designed to enhance the experience of hiking, jungle trekking, cave exploring, and other outdoor activities for groups. Our cutting-edge system enables guides and participants to stay connected and ensures that no one is ever left behind, creating a safer environment for all.
            </p>
            <p>
            Our innovative tracking solution was built with both adventurers and guides in mind. With real-time location monitoring, a proximity alert system, and easy-to-use web management, we empower outdoor enthusiasts to explore with confidence. Every five minutes, our GPS trackers update the location of each group member, providing real-time data and ensuring each individual’s safety. If anyone strays more than 20 meters from the group, an alert buzzer is triggered, helping guides and staff keep everyone within safe range
            <p>
            Beyond just technology, we believe in seamless service. Our platform enables effortless participant registration, real-time tracking, and comprehensive reporting—all managed through a user-friendly interface. Staff and administrators have powerful tools at their fingertips for managing and monitoring activities, ensuring that all essential details are organized and accessible.
            <p>
            We’re here to redefine outdoor group activities with safety, convenience, and peace of mind at the forefront. Whether you're leading a hike through dense jungles or exploring deep caves, we are dedicated to keeping your adventure safe, connected, and enjoyable. Join us in making every journey a safe and memorable experience.
            </p>
            <div class="divider"></div>
            <div class="team">
                <div class="member">
                <center>
                <img src="syamil.jpeg" alt="Team Member">
                    <h3>Ahmad Syamil Bin Safizar</h3>
                    <p>Website Developer</p>
                </center>
                </div>
                <div class="member">
                <center>
                    <img src="afif.jpeg" alt="Team Member">
                    <h3>Muhammad Afif Danish Bin Mohd Fazli</h3>
                    <p>IOT Designer</p>
                </center>
                </div><div class="member">
                <center>
                    <img src="zan.jpeg" alt="Team Member">
                    <h3>Mohd Hafzan Azani Bin Mohd Azamin</h3>
                    <p>Writer and Documentation</p>
                </center>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Smart Tracking Tools For Recreational Climber. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
