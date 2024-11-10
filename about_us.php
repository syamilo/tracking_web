<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - SMART DRYING RACK</title>
   
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
    <div class="container"><h1>SMART DRYING RACK</h1></div>
    </header>
    <section class="about">
        <div class="container">
            <h2>About Us</h2>
            <p>
            Traditional clothes drying methods often have challenges, especially with sudden weather changes like unexpected rain. In such cases, people need to quickly bring in their clothes to prevent them from getting wet again, which can be stressful and requires constant attention.
            </p>
            <p>
            Drying clothes in humid weather with little sunlight can also be difficult, leading to longer drying times or the need to try drying them multiple times. Using electric dryers or fans increases energy use and costs and can sometimes damage delicate fabrics.
            <p>
            Smart technology and automation offer a practical solution to these problems. The Smart Clothes Drying Rack is designed to reduce the need for constant monitoring and manual effort in drying clothes. This system uses sensors like rain sensors and humidity sensors to automatically protect clothes from rain and ensure they are dried properly. With full automation, this drying rack not only saves time but also reduces worries about sudden weather changes.
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
                    <p>Documentation</p>
                </center>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Smart Drying Rack. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
