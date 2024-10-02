<?php
include('database_connection.php');
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $ic_number = $_POST['ic_number'];
    $role = $_POST['role'];

    $query = "INSERT INTO users (name, username, password, ic_number, role) VALUES ('$name', '$username', '$password', '$ic_number', '$role')";
    if ($conn->query($query)) {
        $_SESSION['success_message'] = "New staff/admin added successfully!";
        header("Location: staff_management.php");
        exit();
    }
}
?>

<!-- HTML code remains unchanged -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Staff</title>
    <link rel="stylesheet" href="add_staff.css"> <!-- Linking the CSS file -->
</head>
<body>
    <div class="form-container">
        <h2>Add New Staff</h2>
        <form method="post" action="add_staff.php">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="ic_number" placeholder="IC Number" required>
            <select name="role" required>
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit">Add Staff</button>
        </form>
        <!-- Back button to return to staff management -->
        <div class="back-button-wrapper">
            <a href="staff_management.php" class="back-button">Back to Staff Management</a>
        </div>
    </div>
</body>
</html>
