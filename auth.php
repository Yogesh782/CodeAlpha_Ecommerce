<?php
include 'includes/db.php';
session_start();
$msg = '';
$mode = $_GET['mode'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $pass = $_POST['password'];

        // ✅ Check if reCAPTCHA response exists first
        if (!isset($_POST['g-recaptcha-response'])) {
            $msg = "reCAPTCHA response missing.";
        } else {
            $recaptcha_response = $_POST['g-recaptcha-response'];
            $secret_key = "6Lc5i3srAAAAAKsEOmlTaGwguTVlLVKEO-GtqYs_";

            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$recaptcha_response");
            $response = json_decode($verify);

            if (!$response->success) {
                $msg = "Please verify that you are not a robot.";
            } else {
                $res = $conn->query("SELECT * FROM users WHERE email = '$email'");
                if ($res->num_rows == 1) {
                    $user = $res->fetch_assoc();
                    if (password_verify($pass, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['full_name'];
                        $_SESSION['show_modal'] = true;
                        header("Location: auth.php");
                        exit;
                    } else {
                        $msg = "Invalid password!";
                    }
                } else {
                    $msg = "No user found!";
                }
            }
        }

        $mode = 'login';
    }
    // 🟡 This part (registration) doesn't need captcha unless you want it.
    elseif (isset($_POST['register'])) {
        $name = $_POST['full_name'];
        $email = $_POST['email'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $profilePicName = '';
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $profilePicName = 'user_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], 'uploads/profile_pics/' . $profilePicName);
        }

        $check = $conn->query("SELECT * FROM users WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $msg = "Email already registered!";
        } else {
            $conn->query("INSERT INTO users (full_name, email, password, profile_pic) VALUES ('$name', '$email', '$pass', '$profilePicName')");
            $msg = "Registered successfully. You can now login.";
        }
        $mode = 'register';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login / Register</title>
  <style>
    /* Base Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: url('images/bg-login.jpg') no-repeat center center/cover;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  overflow-x: hidden;
}

/* Navbar */
.navbar {
  background: #1e1e2f;
  color: white;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.navbar .brand {
  font-size: 26px;
  font-weight: bold;
  color: #ffcc00;
  text-decoration: none;
}

.navbar a {
  color: #fff;
  text-decoration: none;
  margin: 0 12px;
  font-weight: 500;
  transition: 0.3s ease;
}

.navbar a:hover {
  color: #ffcc00;
}

/* Overlay Section */
.overlay {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 30px 10px;
}

/* Card Wrapper */
.card-wrapper {
  width: 900px;
  max-width: 100%;
  background: #ffffffdd;
  backdrop-filter: blur(15px);
  border-radius: 12px;
  display: flex;
  box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4);
  overflow: hidden;
  transition: all 0.5s ease;
}

.card-wrapper.login-mode .form-box {
  order: 1;
  animation: slideLeft 0.5s ease forwards;
}
.card-wrapper.login-mode .info-box {
  order: 2;
  animation: slideRight 0.5s ease forwards;
}

.card-wrapper.register-mode .form-box {
  order: 2;
  animation: slideRight 0.5s ease forwards;
}
.card-wrapper.register-mode .info-box {
  order: 1;
  animation: slideLeft 0.5s ease forwards;
}

/* Left Form Section */
.form-box {
  flex: 1;
  padding: 40px;
  background: #fff;
  transition: all 0.5s ease;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.tabs {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-bottom: 20px;
}

.tabs button {
  flex: 1;
  padding: 10px 0;
  background: #eee;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  transition: 0.3s ease;
}

.tabs button.active {
  background: #2ebf91;
  color: white;
  font-weight: bold;
}

/* Form fade-in */
form {
  opacity: 0;
  transform: scale(0.95);
  pointer-events: none;
  position: absolute;
  width: 100%;
}

form.active {
  opacity: 1;
  transform: scale(1);
  pointer-events: all;
  position: relative;
}

/* Tab smooth bg color */
.tabs button {
  transition: background 0.3s ease, color 0.3s ease;
}

input {
  width: 100%;
  padding: 12px;
  margin: 12px 0;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  transition: border-color 0.3s;
}

input:focus {
  outline: none;
  border-color: #2ebf91;
}

button[type="submit"] {
  width: 100%;
  padding: 12px;
  background: #2ebf91;
  border: none;
  color: white;
  font-size: 16px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s;
  transform: scale(1);
}

button[type="submit"]:hover {
  background: #239f7a;
  transform: scale(1.03);
}

.switch-link {
  text-align: center;
  margin-top: 12px;
  font-size: 14px;
}

.switch-link a {
  color: #2ebf91;
  text-decoration: none;
  font-weight: bold;
  cursor: pointer;
}

.switch-link a:hover {
  text-decoration: underline;
}

.msg {
  text-align: center;
  color: red;
  font-size: 14px;
  margin-top: 10px;
}

/* Right Info Section */
.info-box {
  flex: 1;
  background: #444;
  color: white;
  padding: 40px 30px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  transition: all 0.5s ease;
}

.logo-box img.logo {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  margin-bottom: 10px;
}

.logo-box h2 {
  font-size: 24px;
  font-weight: bold;
  margin: 10px 0 5px;
}

.logo-box p {
  font-size: 14px;
  color: #ccc;
}

.about-text {
  margin-top: 20px;
  font-size: 15px;
  line-height: 1.6;
}

.social-icons {
  margin-top: 25px;
}

.social-icons i {
  font-size: 20px;
  margin: 0 10px;
  color: #ccc;
  transition: 0.3s;
  cursor: pointer;
  transition: color 0.3s ease, transform 0.3s ease;
}

.social-icons i:hover {
  color: #2ebf91;
  transform: translateY(-3px);
}

/* Welcome modal */
#loginModal {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) scale(1);
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  width: 400px;
  max-width: 90%;
  padding: 40px 30px;
  border-radius: 16px;
  text-align: center;
  z-index: 9999;
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
  animation: popUp 0.5s ease;
}

#loginModal h2 {
  font-size: 26px;
  color: #2ebf91;
  margin: 15px 0 10px;
}

#loginModal p {
  font-size: 16px;
  color: #333;
  margin-bottom: 25px;
}

#loginModal button {
  background: #2ebf91;
  color: white;
  padding: 12px 30px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
}

#loginModal button:hover {
  background: #239f7a;
  transform: scale(1.05);
}

#loginModal .profile-pic {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
  box-shadow: 0 0 8px rgba(0,0,0,0.2);
  border: 2px solid #fff;
  margin-bottom: 15px;
}

@keyframes popUp {
  0% {
    opacity: 0;
    transform: translate(-50%, -60%) scale(0.9);
  }
  100% {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .card-wrapper {
    flex-direction: column;
    width: 100%;
    margin: 10px;
  }

  .form-box,
  .info-box {
    width: 100%;
    order: unset !important;
  }

  .tabs {
    gap: 5px;
  }

  .logo-box h2 {
    font-size: 20px;
  }

  .logo-box img.logo {
    width: 60px;
    height: 60px;
  }

  .about-text {
    font-size: 14px;
  }

  .social-icons i {
    font-size: 18px;
  }
}

/* Animations */
@keyframes slideLeft {
  0% {
    transform: translateX(100px);
    opacity: 0;
  }
  100% {
    transform: translateX(0px);
    opacity: 1;
  }
}

@keyframes slideRight {
  0% {
    transform: translateX(-100px);
    opacity: 0;
  }
  100% {
    transform: translateX(0px);
    opacity: 1;
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
.g-recaptcha {
  margin: 12px 0;
}

  </style>
</head>
<body>

<!-- ✅ Navbar (Login/Register buttons hidden on this page) -->
<div class="navbar">
  <a href="index.php" class="brand">🛍 CodeAlpha</a>
  <div class="right">
    <a href="index.php">Home</a>
    <a href="cart.php">Cart</a>
    <a href="myorder.php">My Orders</a>
  </div>
</div>

<!-- ✅ Form container with overlay -->
<div class="overlay">
  <div class="card-wrapper <?= $mode === 'register' ? 'register-mode' : 'login-mode' ?>">
    <!-- Left Panel -->
    <div class="form-box">
      <div class="tabs">
        <button id="loginTab" class="<?= $mode === 'login' ? 'active' : '' ?>">Login</button>
        <button id="registerTab" class="<?= $mode === 'register' ? 'active' : '' ?>">Register</button>
      </div>

      <!-- Login Form -->
      <form id="loginForm" method="POST" class="<?= $mode === 'login' ? 'active' : '' ?>">
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password" required>

  <!-- Google reCAPTCHA box -->
  <div class="g-recaptcha" data-sitekey="6Lc5i3srAAAAAAlfztLZGWEkAl1DdEYiTXeGPA1N"></div> <!-- 🛑 Replace site key -->

  <button type="submit" name="login">Login</button>
  <div class="switch-link">Don't have an account? <a onclick="showRegister()">Register here</a></div>
</form>


      <!-- Register Form -->
      <form id="registerForm" method="POST" enctype="multipart/form-data" class="<?= $mode === 'register' ? 'active' : '' ?>">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="file" name="profile_pic" accept="image/*" required>
        <button type="submit" name="register">Register</button>
        <div class="switch-link">Already have an account? <a onclick="showLogin()">Login here</a></div>
      </form>

      <div class="msg"><?= $msg; ?></div>
    </div>

    <!-- Right Panel -->
    <div class="info-box">
      <div class="logo-box">
        <img src="images/logo.png" alt="CodeAlpha Logo" class="logo">
        <h2>CODEALPHA STORE</h2>
        <p>Your gateway to smart & secure ecommerce solutions.</p>
      </div>
      <div class="about-text">
        <p>Welcome to CodeAlpha — we create innovative digital solutions, providing top-tier services in ecommerce, learning, and software development.</p>
      </div>
      <div class="social-icons">
        <i class="fab fa-instagram"></i>
        <i class="fab fa-facebook-f"></i>
        <i class="fab fa-twitter"></i>
        <i class="fab fa-linkedin-in"></i>
      </div>
    </div>
  </div>
</div>
<?php if (isset($_SESSION['show_modal']) && $_SESSION['show_modal']): ?>
<?php
  $userId = $_SESSION['user_id'];
  $result = $conn->query("SELECT * FROM users WHERE id = $userId");
  $user = $result->fetch_assoc();
  $profilePic = $user['profile_pic'] ? 'uploads/profile_pics/' . $user['profile_pic'] : 'images/default-user.png';
?>
<div id="loginModal" class="active">
  <img src="<?= $profilePic ?>" alt="Profile Picture" class="profile-pic">
  <h2>Welcome, <?= htmlspecialchars($user['full_name']) ?> 🎉</h2>
  <p>You have successfully logged in.</p>
  <button onclick="closeModal()">Continue</button>
</div>
<script>
  function closeModal() {
    window.location.href = 'index.php';
  }

  setTimeout(closeModal, 3000); // auto redirect in 3 sec
</script>
<?php unset($_SESSION['show_modal']); endif; ?>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
  const loginTab = document.getElementById('loginTab');
  const registerTab = document.getElementById('registerTab');
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  const wrapper = document.querySelector('.card-wrapper'); // optional

  function showLogin() {
    loginTab.classList.add('active');
    registerTab.classList.remove('active');
    loginForm.classList.add('active');
    registerForm.classList.remove('active');

    // Optional: If you use class-based animation
    if (wrapper) {
      wrapper.classList.remove('register-mode');
      wrapper.classList.add('login-mode');
    }
  }

  function showRegister() {
    registerTab.classList.add('active');
    loginTab.classList.remove('active');
    registerForm.classList.add('active');
    loginForm.classList.remove('active');

    if (wrapper) {
      wrapper.classList.remove('login-mode');
      wrapper.classList.add('register-mode');
    }
  }

  loginTab.addEventListener('click', showLogin);
  registerTab.addEventListener('click', showRegister);
</script>

</body>
</html>
