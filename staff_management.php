<?php
include('database_connection.php');
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle the delete action
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delete_query = "DELETE FROM users WHERE id='$delete_id'";
    if ($conn->query($delete_query)) {
        $_SESSION['success_message'] = 'User deleted successfully.';
    } else {
        $_SESSION['success_message'] = 'Error deleting user.';
    }
    header("Location: staff_management.php");
    exit();
}

// Fetch all staff and admin users
$query = "SELECT * FROM users WHERE role='staff' OR role='admin'";
$result = $conn->query($query);

// Display success message if available
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Remove the message after displaying it
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff and Admin Management</title>
    <link rel="stylesheet" href="staff_management.css">
    <script>
        // Function to confirm deletion
        function confirmDelete() {
            return confirm('Are you sure you want to delete this user?');
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Staff and Admin Management</h2>

        <!-- Display success message -->
        <?php if ($success_message): ?>
            <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="edit-button">Edit</a>
                            <form method="post" style="display:inline;" onsubmit="return confirmDelete();">
                                <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="delete-button">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="add_staff.php" class="add-button">Add New Staff</a>
        <a href="admin.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
