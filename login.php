<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['password'])) {
    require_once 'db.php';
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);

    // Check if the login credentials match the predefined admin credentials
//    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit;
    } else {

        // Fetch user from the database
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // Check if user exists
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start a session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: dashboard.php"); // Redirect to dashboard
                exit;
            } else {
                session_destroy();
                echo "Invalid username or password!";
            }
        } else {
            session_destroy();
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