<?php
session_start();
$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .thank-you {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 90%;
        }
        .thank-you h1 {
            font-size: 32px;
            color: #27ae60;
            margin-bottom: 20px;
        }
        .thank-you p {
            font-size: 18px;
            color: #555;
            margin: 10px 0;
        }
        .thank-you a.button {
            display: inline-block;
            margin-top: 25px;
            background: #1e1e2f;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
        }
        .thank-you a.button:hover {
            background: #333;
        }
    </style>
</head>
<body>

<!-- Include navbar -->
<?php include 'includes/navbar.php'; ?>

<main>
    <div class="thank-you">
        <h1>🎉 Thank You for Your Order!</h1>
        <p>Your Order ID is <strong>#<?= htmlspecialchars($order_id) ?></strong></p>
        <p>You will receive a confirmation email shortly.</p>
        <a href="index.php" class="button">Continue Shopping</a>
    </div>
</main>

<!-- Include footer -->
<?php include 'includes/footer.php'; ?>

</body>
</html>
