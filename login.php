<?php
include('database_connection.php'); // Including the database connection file
session_start();

$error = "";
$success = "";
$show_forgot_password = false;  // Control when to show the forgot password form

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle the login form
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Prevent SQL Injection by using prepared statements
        $query = $conn->prepare("SELECT * FROM users WHERE username=? AND role=?");
        $query->bind_param("ss", $username, $role);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Successful login
                $_SESSION['role'] = $user['role'];
                $_SESSION['username'] = $user['name'];

                // Redirect to the appropriate page based on role
                if ($user['role'] == 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: staff.php");
                }
                exit();
            } else {
                // Invalid password
                $error = "Invalid username, password, or role! <a href='javascript:void(0);' onclick='showForgotPasswordForm();'>Forgot Password?</a>";
                $_SESSION['forgot_ic'] = $user['ic_number'];
            }
        } else {
            // Invalid username or role
            $error = "Invalid username, password, or role! <a href='javascript:void(0);' onclick='showForgotPasswordForm();'>Forgot Password?</a>";
        }
    }
    // Handle the password reset form
    if (isset($_POST['reset_password'])) {
        $ic_number = $_POST['ic_number'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        // Prepared statement for security
        $query = $conn->prepare("UPDATE users SET password=? WHERE ic_number=?");
        $query->bind_param("ss", $new_password, $ic_number);
        if ($query->execute() && $query->affected_rows > 0) {
            $success = "Password reset successful. Please log in with your new password.";
            $show_forgot_password = false;  // Hide forgot password form after reset
            header("Location: login.php?success=" . urlencode($success));  // Redirect back to login form with success message
            exit();
        } else {
            $error = "Invalid IC number!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
    <script>
        function showForgotPasswordForm() {
            document.getElementById('forgot-password-form').style.display = 'block';
            document.getElementById('login-form').style.display = 'none';
        }

        function showLoginForm() {
            document.getElementById('forgot-password-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        }
    </script>
</head>
<body>
    <!-- Login Form -->
    <form id="login-form" method="post" action="login.php" style="display: <?= $show_forgot_password ? 'none' : 'block' ?>;">
        <h2>Login</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit" name="login">Login</button>
        <!-- Error Message (shows if login fails) -->
        <?php if (isset($error)) { ?>
            <p style="color:red"><?= $error ?></p>
        <?php } ?>
        <!-- Success Message (shows if password reset is successful) -->
        <?php if (isset($_GET['success'])) { ?>
            <p style="color:green"><?= htmlspecialchars($_GET['success']) ?></p>
        <?php } ?>
    </form>
    <!-- Forgot Password Form (hidden by default, shows when 'Forgot Password?' is clicked) -->
    <form id="forgot-password-form" method="post" action="login.php" style="display: none;">
        <h3>Reset Password</h3>
        <p>Enter your IC number and set a new password.</p>
        <input type="text" name="ic_number" placeholder="IC Number" value="<?= isset($_SESSION['forgot_ic']) ? $_SESSION['forgot_ic'] : '' ?>" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <button type="submit" name="reset_password">Reset Password</button>
        <!-- Error Message (shows if IC number is invalid) -->
        <?php if (isset($error) && strpos($error, 'IC number') !== false) { ?>
            <p style="color:red"><?= $error ?></p>
        <?php } ?>
    </form>
</body>
</html>
