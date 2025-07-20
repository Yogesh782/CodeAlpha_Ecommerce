<?php
session_start();
include 'includes/db.php';
include 'includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders with joined products
$query = "SELECT o.id AS order_id, o.status, o.order_date, 
                 p.name AS product_name, p.image AS product_image, 
                 oi.price, oi.quantity
          FROM orders o
          JOIN order_items oi ON o.id = oi.order_id
          JOIN products p ON p.id = oi.product_id
          WHERE o.user_id = $user_id
          ORDER BY o.order_date DESC";

$result = $conn->query($query);

// Debug query error
if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f5f5;
            margin: 0;
        }

        .orders-container {
            padding: 30px;
            max-width: 1000px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
        }

        .order-card {
            background: #fff;
            color: #333;
            border-left: 5px solid #007bff;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .order-card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 20px;
        }

        .order-info {
            flex: 1;
        }

        .order-info h4 {
            margin: 0;
            font-size: 18px;
            color: #007bff;
        }

        .order-info small {
            display: block;
            margin-top: 5px;
            color: #777;
        }

        .order-status {
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 6px;
            text-transform: capitalize;
            min-width: 90px;
            text-align: center;
        }

        .status-pending {
            background: #ffc107;
            color: #000;
        }

        .status-success {
            background: #28a745;
            color: #fff;
        }

        .status-failed {
            background: #dc3545;
            color: #fff;
        }

        @media screen and (max-width: 600px) {
            .order-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-card img {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

<div class="orders-container">
    <h2>🧾 My Orders</h2>

    <?php
    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
            $status_class = 'status-' . strtolower($row['status']);
    ?>
        <div class="order-card">
            <img src="uploads/<?= htmlspecialchars($row['product_image']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>">
            <div class="order-info">
                <h4><?= htmlspecialchars($row['product_name']) ?></h4>
                <small>₹<?= number_format($row['price']) ?> • Quantity: <?= $row['quantity'] ?></small>
                <small>Ordered on <?= date("d M, Y", strtotime($row['order_date'])) ?></small>
            </div>
            <div class="order-status <?= $status_class ?>">
                <?= ucfirst($row['status']) ?>
            </div>
        </div>
    <?php
        endwhile;
    else:
        echo "<p style='text-align:center; font-size:18px; color:#666;'>🛒 You have no orders yet.</p>";
    endif;
    ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
