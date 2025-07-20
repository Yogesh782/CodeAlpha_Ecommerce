<?php
session_start();
include 'includes/db.php';
include 'includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_SESSION['user_id'];
    $name = $conn->real_escape_string($_POST['full_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $alt_phone = $conn->real_escape_string($_POST['alt_phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $payment = $conn->real_escape_string($_POST['payment_method']);

    $total = 0;

    foreach ($_SESSION['cart'] as $id => $qty) {
        $res = $conn->query("SELECT price FROM products WHERE id=$id");
        $price = $res->fetch_assoc()['price'];
        $total += $qty * $price;
    }

    // Save order
    $conn->query("INSERT INTO orders (user_id, full_name, phone, alt_phone, address, payment_method, total) 
        VALUES ('$uid', '$name', '$phone', '$alt_phone', '$address', '$payment', '$total')");

    $order_id = $conn->insert_id;

    // Save each item
    foreach ($_SESSION['cart'] as $id => $qty) {
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity) VALUES ($order_id, $id, $qty)");
    }

    unset($_SESSION['cart']);
    header("Location: order_success.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Order</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<style>
    .order-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 20px;
}

.order-form input, .order-form textarea, .order-form select {
    padding: 10px;
    font-size: 16px;
    border-radius: 6px;
    border: 1px solid #ccc;
    width: 100%;
}

.order-form label {
    font-weight: bold;
}

.order-container {
    background: #fff;
    max-width: 500px;
    margin: 80px auto;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-radius: 10px;
    text-align: left;
}

.btn-confirm {
    background-color: #28a745;
    border: none;
    padding: 12px 25px;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.3s ease;
}

</style>
<body>
    <div class="order-container">
        <h2>Confirm Your Order</h2>
        <form method="POST" class="order-form">
            <label>Full Name</label>
            <input type="text" name="full_name" required>

            <label>Phone Number (Primary)</label>
            <input type="tel" name="phone" required pattern="[0-9]{10}" placeholder="10-digit mobile number">

            <label>Alternate Number (Optional)</label>
            <input type="tel" name="alt_phone" pattern="[0-9]{10}">

            <label>Delivery Address</label>
            <textarea name="address" rows="4" required></textarea>

            <label>Payment Method</label>
            <select name="payment_method" required>
                <option value="COD">Cash on Delivery</option>
                <option value="Online">Online Payment</option>
            </select>

            <button type="submit" class="btn-confirm">Confirm Order</button>
        </form>
    </div>
</body>
</html>
