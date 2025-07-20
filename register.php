<?php
include 'includes/db.php';
session_start();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $msg = "Email already registered!";
    } else {
        $conn->query("INSERT INTO users (full_name, email, password) VALUES ('$name', '$email', '$pass')");
        $msg = "Registered successfully. <a href='login.php'>Login here</a>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
<h2>User Registration</h2>
<form method="POST">
  <input type="text" name="full_name" placeholder="Full Name" required><br>
  <input type="email" name="email" placeholder="Email" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <button type="submit">Register</button>
</form>
<p style="color:red;"><?= $msg; ?></p>
<p>Already have an account? <a href="login.php">Login</a></p>
</body>
</html>
