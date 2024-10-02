<?php
include('database_connection.php');
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$result = $conn->query($query);
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $ic_number = $_POST['ic_number'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // Update the user details
    $query = "UPDATE users SET name='$name', username='$username', ic_number='$ic_number', role='$role'";

    // If a new password is provided, hash it and update it
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password='$hashed_password'";
    }

    $query .= " WHERE id='$id'";

    if ($conn->query($query)) {
        $_SESSION['success_message'] = "Staff/admin details updated successfully!";
        header("Location: staff_management.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff/Admin</title>
    <link rel="stylesheet" href="edit.css">
</head>
<body>
    <div class="form-container">
        <h2>Edit Staff/Admin</h2>
        <form method="post" action="edit.php?id=<?= $id ?>">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="ic_number">IC Number</label>
                <input type="text" name="ic_number" id="ic_number" value="<?= htmlspecialchars($user['ic_number']) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">New Password (leave blank to keep current)</label>
                <input type="password" name="password" id="password" placeholder="Enter new password">
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" id="role">
                    <option value="staff" <?= $user['role'] == 'staff' ? 'selected' : '' ?>>Staff</option>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="submit-button">Update</button>
        </form>
        <a href="staff_management.php" class="back-button">Back to Staff Management</a>
    </div>
</body>
</html>
