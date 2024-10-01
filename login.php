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
        $username = test_input($_POST['username']);
        $password = test_input($_POST['password']);

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
                $_SESSION['role'] = $user['role'];

                // Redirect based on the role
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit;
            } else {
                $error_message = '<p class="alert alert-danger font-bold">Invalid Password!</p>';
            }
        } else {
            $error_message = '<p class="alert alert-danger font-bold">No user found with this username and password!</p>';
        }
    }
}
?>

<!DOCTYPE html>
<head>
    <title>Login</title>
    <link type="text/css" rel="stylesheet" href="assets/css/aqua.min.css">
    <link rel="stylesheet" href="assets/css/login.scss">
</head>
<body>
<?php if (isset($error_message)) echo $error_message; ?>
<form class="login-form d-flex" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <h1>Login</h1>
    <div class="form-input-material">
        <input type="text" name="username" id="username" placeholder=" " value="<?php echo $_POST['username'] ?? '' ?>" autocomplete="off" class="form-control-material" required />
        <label for="username">Username</label>
    </div>
    <div class="form-input-material">
        <input type="password" name="password" id="password" placeholder=" " autocomplete="off" class="form-control-material" required />
        <label for="password">Password</label>
    </div>
    <button type="submit" class="btn btn-primary btn-ghost">Login</button>
</form>
</body>