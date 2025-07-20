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

// Handle form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $total = 0;

    // Calculate total
    foreach ($_SESSION['cart'] as $id => $qty) {
        $res = $conn->query("SELECT * FROM products WHERE id=$id");
        $product = $res->fetch_assoc();
        $total += $qty * $product['price'];
    }

    // Insert into orders table
    $conn->query("INSERT INTO orders (user_id, name, phone, address, total_amount, status, order_date)
                  VALUES ('$user_id', '$name', '$phone', '$address', '$total', 'Pending', NOW())");

    $order_id = $conn->insert_id; // Last inserted order ID

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
    $message = "Dear $name,\n\nThank you for your order.\nYour order ID is $order_id.\nTotal: ₹$total\nWe will contact you soon.\n\n- CodeAlpha Store";
    @mail($user_email, $subject, $message); // (Optional)

    // Clear cart
    unset($_SESSION['cart']);

    // Redirect to thank you page
    header("Location: thankyou.php?order_id=$order_id");
    exit;
}
?>
