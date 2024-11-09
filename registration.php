<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration</title>
    <link rel="stylesheet" href="registration.css">
    <style>
        /* Center container and set max-width for a more compact design */
        .form-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Title styling */
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        /* Two-column grid layout for form fields */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Styling individual form groups */
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        /* Button styles */
        .submit-button, .back-button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .back-button {
            background-color: #6c757d;
        }

        .submit-button:hover, .back-button:hover {
            opacity: 0.9;
        }

        /* Success message styling */
        .success-message {
            color: green;
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
    <script>
        function fetchAvailableDevices() {
            const bookingDate = document.getElementById('booking_date').value;
            window.location.href = `registration.php?booking_date=${bookingDate}`;
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Register Customer</h2>

        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>

        <form method="post" action="registration.php">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" name="age" id="age" required>
                </div>
                <div class="form-group">
                    <label for="ic_number">IC Number</label>
                    <input type="text" name="ic_number" id="ic_number" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" required>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" required>
                </div>
                <div class="form-group">
                    <label for="booking_date">Booking Date</label>
                    <input type="date" name="booking_date" id="booking_date" value="<?= htmlspecialchars($booking_date) ?>" required onchange="fetchAvailableDevices()">
                </div>
                <div class="form-group">
                    <label for="color">Choose a Color</label>
                    <select name="color" id="color" required>
                        <option value="Red">Red</option>
                        <option value="Blue">Blue</option>
                        <option value="Green">Green</option>
                        <option value="Yellow">Yellow</option>
                        <option value="Orange">Orange</option>
                        <option value="Magenta">Magenta</option>
                        <option value="Pink">Pink</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="activity">Activity</label>
                    <input type="text" name="activity" id="activity" required>
                </div>
                <div class="form-group">
                    <label for="gps_device_id">Choose GPS Device</label>
                    <select name="gps_device_id" id="gps_device_id" required>
                        <option value="">Select a GPS Device</option>
                        <?php foreach ($available_devices as $device): ?>
                            <option value="<?= $device['id'] ?>"><?= htmlspecialchars($device['device_ID']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="submit-button">Register</button>
        </form>
        <a href="staff.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
