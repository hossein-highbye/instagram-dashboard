<?php
session_start();
require_once "db.php";

// Check if session is already set
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    // Redirect based on the role
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php"); // Redirect to admin dashboard
    } else {
        header("Location: user_dashboard.php"); // Redirect to user dashboard
    }
    exit;
}

// If no session is set, proceed with login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Sanitize input
        $username = htmlspecialchars(trim($_POST['username']));
        $password = trim($_POST['password']);

        // Fetch user from the database
        $sql = "SELECT id, username, password, role FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // Check if the user exists
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start a session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role']; // Store the role in the session

                // Redirect based on the role
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php"); // Redirect to admin dashboard
                } else {
                    header("Location: user_dashboard.php"); // Redirect to user dashboard
                }
                exit;
            } else {
                echo "Invalid username or password!";
            }
        } else {
            echo "Invalid username or password!";
        }
    }
}
?>

<!DOCTYPE html>
<head>
    <title>Login</title>
</head>
<body>
<form action="" method="POST">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Login</button>
</form>
</body>