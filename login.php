<?php
include 'includes/db.php';
session_start();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($res->num_rows == 1) {
        $user = $res->fetch_assoc();
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            header("Location: index.php");
            exit;
        } else {
            $msg = "Invalid password!";
        }
    } else {
        $msg = "No user found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
<h2>User Login</h2>
<form method="POST">
  <input type="email" name="email" placeholder="Email" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <button type="submit">Login</button>
</form>
<p style="color:red;"><?= $msg; ?></p>
<p>Don't have an account? <a href="register.php">Register</a></p>
</body>
</html>
