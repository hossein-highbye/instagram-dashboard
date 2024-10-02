<?php
session_start();

// Check if the admin is logged in
if ($_SESSION['admin_logged_in'] === false && $_SESSION['role'] === false) {
    header("Location: login.php");
    exit;
}

require_once "db.php";

// Handle user creation and deletion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['create_user'])) {
        // Sanitize and validate input
        $new_username = test_input($_POST['new_username']);
        $new_password = test_input($_POST['new_password']);
        $new_role = test_input($_POST['role']);

        if (strlen($new_username) < 5 || strlen($new_password) < 6) {
            $error_message = '<p class="alert alert-danger">Username must be at least 5 characters and password 6 characters.</p>';
        } else {
            // Hash the password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $new_username);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':role', $new_role);

            if ($stmt->execute()) {
                $error_message = '<p class="alert alert-success">User created successfully!</p>';
            } else {
                $error_message = '<p class="alert alert-danger">Error creating user.</p>';
            }
        }
    } elseif (isset($_POST['delete_user'])) {
        // Delete user from the database
        $user_id = $_POST['user_id'];

        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $user_id);

        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $error_message = '<p class="alert alert-success">User deleted successfully!</p>';
            } else {
                $error_message = '<p class="alert alert-danger">No user found with this ID!</p>';
            }
        } else {
            $error_message = '<p class="alert alert-danger">Error deleting user.</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="data:image/svg+xml,%3csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2033%2034'%20fill-rule='evenodd'%20stroke-linejoin='round'%20stroke-miterlimit='2'%20xmlns:v='https://vecta.io/nano'%3e%3cpath%20d='M3%2027.472c0%204.409%206.18%205.552%2013.5%205.552%207.281%200%2013.5-1.103%2013.5-5.513s-6.179-5.552-13.5-5.552c-7.281%200-13.5%201.103-13.5%205.513z'%20fill='%23435ebe'%20fill-rule='nonzero'/%3e%3ccircle%20cx='16.5'%20cy='8.8'%20r='8.8'%20fill='%2341bbdd'/%3e%3c/svg%3e" type="image/x-icon">
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC" type="image/png">

    <link rel="stylesheet" href="/dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/dist/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="/dist/assets/compiled/css/iconly.css">
    <link rel="stylesheet" type="text/css" href="assets/css/admin.min.css">
    <title>Admin Dashboard</title>
</head>

<body>
<script src="/dist/assets/static/js/initTheme.js"></script>
<div id="app">
    <div id="main" style="margin-left: 0">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="d-flex col-6 col-md-6">
                        <h3>Existing users</h3>
                    </div>
                    <div class="d-flex col-6 col-md-6 justify-content-end">
                        <a href="logout.php">
                            <button class="btn btn-primary">Logout</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Basic Tables start -->
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            <tbody>
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <!-- Basic Tables end -->
        <section class="section d-flex mt-5">
            <div class="row w-100">
                <div class="col-12 col-lg-6 col-xl-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="row justify-content-center align-items-center custom-height mb-5">
                        <div class="d-flex flex-column h-100 w-50 p-5 justify-content-between border-glass w-75">
                            <h2 class="mb-5">Create New User</h2>
                            <form action="" method="POST" class="d-flex flex-column form-group">
                                <div class="my-2">
                                    <label for="new_username">Username</label>
                                    <input class="form-control" type="text" id="new_username" name="new_username" required>
                                </div>
                                <div class="my-2">
                                    <label for="new_password">Password</label>
                                    <input class="form-control" type="password" id="new_password" name="new_password" required>
                                </div>
                                <div class="d-flex my-2 justify-content-between align-items-center">
                                    <label>Role</label>
                                    <div>
                                        <div>
                                            <input class="mx-2" id="userrole" type="radio" checked="checked" name="role" value="user"><label for="userrole">User</label>
                                        </div>
                                        <div>
                                            <input class="mx-2" id="adminrole" type="radio" name="role" value="admin"><label for="adminrole">Admin</label>
                                        </div>
                                    </div>
                                </div>

                                <button class="btn btn-primary my-2" type="submit" name="create_user">Create User</button>

                                <?php if (isset($_POST['create_user']) && isset($error_message)) echo $error_message ?>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="row justify-content-center align-items-center custom-height mb-5">
                        <div class="d-flex flex-column h-100 w-50 p-5 justify-content-between border-glass w-75">
                            <h2 class="mb-5">Delete Existing Users</h2>
                            <form action="" method="POST" class="d-flex flex-column form-group">
                                <div class="my-2">
                                    <label for="user_id">User ID</label>
                                    <input class="form-control" type="number" id="user_id" name="user_id" required>
                                </div>

                                <button class="btn btn-primary my-2" type="submit" name="delete_user">Delete User</button>
                                <?php if (isset($_POST['delete_user']) && isset($error_message)) echo $error_message ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="dist/assets/static/js/components/dark.js"></script>
<script src="dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>

<script src="dist/assets/compiled/js/app.js"></script>

<script src="dist/assets/extensions/jquery/jquery.min.js"></script>
<script src="dist/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="dist/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="dist/assets/static/js/pages/datatables.js"></script>

</body>
</html>