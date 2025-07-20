<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fetch profile picture if user is logged in
$profile_pic = '';
if (isset($_SESSION['user_id'])) {
    include 'includes/db.php';
    $uid = $_SESSION['user_id'];
    $res = $conn->query("SELECT profile_pic FROM users WHERE id = $uid");
    $row = $res->fetch_assoc();
    if (!empty($row['profile_pic']) && file_exists("uploads/profile_pics/" . $row['profile_pic'])) {
        $profile_pic = "uploads/profile_pics/" . $row['profile_pic'];
    } else {
        $profile_pic = "uploads/profile_pics/default.png"; // fallback image
    }
}
?>
<style>
    .navbar {
        background: #1e1e2f;
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    .navbar .brand {
        font-size: 24px;
        font-weight: bold;
        color: #ffcc00;
        text-decoration: none;
    }
    .navbar a {
        color: #fff;
        text-decoration: none;
        margin: 0 12px;
        font-weight: 500;
        transition: 0.3s;
    }
    .navbar a:hover {
        color: #ffcc00;
    }
    .navbar .right {
        display: flex;
        align-items: center;
    }
    .navbar .btn {
        padding: 8px 15px;
        border-radius: 5px;
        font-weight: bold;
        margin-left: 10px;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-login {
        background: #28a745;
        color: white;
        border: none;
    }
    .btn-login:hover {
        background: #218838;
    }
    .btn-logout {
        background: #dc3545;
        color: white;
        border: none;
    }
    .btn-logout:hover {
        background: #c82333;
    }
    .profile-pic {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
        margin-left: 15px;
        border: 2px solid #ffcc00;
    }
    @media screen and (max-width: 768px) {
        .navbar {
            flex-direction: column;
            align-items: flex-start;
        }
        .navbar .right {
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }
        .navbar a, .navbar .btn {
            margin: 8px 0;
        }
    }
</style>

<div class="navbar">
    <a href="index.php" class="brand">🛍 CodeAlpha</a>
    <div class="right">
        <a href="index.php">Home</a>
        <a href="cart.php">Cart</a>
        <a href="myorder.php">My Order</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span style="color: #fff; margin-left: 12px;">Welcome, <?= $_SESSION['user_name']; ?></span>
            <img src="<?= $profile_pic ?>" class="profile-pic" alt="Profile">
            <a href="logout.php" class="btn btn-logout">Logout</a>
        <?php else: ?>
            <a href="auth.php" class="btn btn-login">Login / Register</a>
        <?php endif; ?>
    </div>
</div>
