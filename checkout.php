<?php
session_start();
include 'includes/db.php';

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit;
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    echo "<script>alert('Cart is empty!'); window.location.href='cart.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $alt_phone = $_POST['alt_phone'] ?? '';
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'] ?? 'COD';
    $total = 0;

    foreach ($_SESSION['cart'] as $id => $qty) {
        $res = $conn->query("SELECT * FROM products WHERE id=$id");
        if ($res->num_rows > 0) {
            $product = $res->fetch_assoc();
            $total += $qty * $product['price'];
        }
    }

    // Insert into orders table
    $conn->query("INSERT INTO orders (user_id, full_name, phone, alt_phone, address, payment_method, total, order_date)
                  VALUES ('$user_id', '$name', '$phone', '$alt_phone', '$address', '$payment_method', '$total', NOW())");

    $order_id = $conn->insert_id;

    // Insert order items
    foreach ($_SESSION['cart'] as $id => $qty) {
        $res = $conn->query("SELECT * FROM products WHERE id=$id");
        $product = $res->fetch_assoc();
        $price = $product['price'];
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price)
                      VALUES ('$order_id', '$id', '$qty', '$price')");
    }

    // Optional: Send confirmation email
    $user_email = $conn->query("SELECT email FROM users WHERE id=$user_id")->fetch_assoc()['email'];
    $subject = "Your Order #$order_id has been placed!";
    $message = "Dear $name,\n\nThank you for your order.\nYour Order ID: $order_id\nTotal: ₹$total\nWe'll contact you soon.\n\n- CodeAlpha Store";
    @mail($user_email, $subject, $message);

    unset($_SESSION['cart']);
    header("Location: thankyou.php?order_id=$order_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - CodeAlpha Store</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background-color: #1e1e2f;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #333;
        }

        .total {
            text-align: right;
            font-size: 18px;
            color: green;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container">
    <h2>Checkout</h2>
    <form method="POST">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" required>

        <label for="phone">Phone Number</label>
        <input type="text" name="phone" id="phone" required>

        <label for="alt_phone">Alternate Phone</label>
        <input type="text" name="alt_phone" id="alt_phone">

        <label for="address">Delivery Address</label>
        <textarea name="address" id="address" rows="4" required></textarea>

        <label for="payment_method">Payment Method</label>
        <select name="payment_method" id="payment_method">
            <option value="COD">Cash on Delivery</option>
            <option value="Online">Online Payment</option>
        </select>

        <button type="submit">Place Order</button>
    </form>
</div>
</body>
</html>
