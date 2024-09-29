<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login_form.html");
    exit;
}

require_once "db.php";

// Handle user creation and deletion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['create_user'])) {
        // Sanitize and validate input
        $new_username = htmlspecialchars(trim($_POST['new_username']));
        $new_password = trim($_POST['new_password']);

        if (strlen($new_username) < 5 || strlen($new_password) < 6) {
            echo "Username must be at least 5 characters and password 6 characters.";
        } else {
            // Hash the password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $new_username);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
                echo "User created successfully!";
            } else {
                echo "Error creating user.";
            }
        }
    } elseif (isset($_POST['delete_user'])) {
        // Delete user from the database
        $user_id = $_POST['user_id'];

        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $user_id);

        if ($stmt->execute()) {
            echo "User deleted successfully!";
        } else {
            echo "Error deleting user.";
        }
    }
}
?>
<!DOCTYPE html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
<h2>Existing Users</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
        <th>Created At</th>
    </tr>
    <?php
    $sql = "SELECT id, username, role, created_at FROM users";
    $stmt = $pdo->query($sql);

    while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['username'] . "</td>";
        echo "<td>" . $user['role'] . "</td>";
        echo "<td>" . $user['created_at'] . "</td>";
        echo "</tr>";
    }
    ?>
</table>

<form action="admin_dashboard.php" method="POST">
    <h2>Create New User</h2>
    <label for="new_username">Username</label>
    <input type="text" id="new_username" name="new_username" required>

    <label for="new_password">Password</label>
    <input type="password" id="new_password" name="new_password" required>

    <button type="submit" name="create_user">Create User</button>
</form>

<h2>Delete Existing Users</h2>
<form action="admin_dashboard.php" method="POST">
    <label for="user_id">User ID</label>
    <input type="number" id="user_id" name="user_id" required>

    <button type="submit" name="delete_user">Delete User</button>
</form>

<a href="logout.php">Logout</a>

</body>