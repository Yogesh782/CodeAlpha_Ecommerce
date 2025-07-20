<?php
session_start();
include 'includes/db.php';


// Add to cart logic
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please login to add products to cart.'); window.location.href='auth.php';</script>";
        exit;
    }
    $id = $_POST['product_id'];
    $qty = $_POST['quantity'];
    $_SESSION['cart'][$id] = $_SESSION['cart'][$id] ?? 0;
    $_SESSION['cart'][$id] += $qty;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart - CodeAlpha Store</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f7f7f7;
      margin: 0;
      padding: 0;
    }

    .cart-container {
      max-width: 900px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #222;
    }

    .cart-items {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .cart-item {
      display: flex;
      gap: 20px;
      padding: 15px;
      border: 1px solid #eee;
      border-radius: 10px;
      background: #fafafa;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .cart-item img {
      width: 120px;
      height: 160px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #ccc;
    }

    .item-details {
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .item-details h3 {
      margin: 0;
      font-size: 20px;
      color: #333;
    }

    .item-details p {
      margin: 5px 0;
      font-size: 15px;
      color: #555;
    }

    .total {
      text-align: right;
      font-size: 22px;
      font-weight: bold;
      margin-top: 30px;
      color: #27ae60;
    }

    .checkout-btn {
      display: block;
      text-align: center;
      margin-top: 30px;
      background: #1e1e2f;
      color: #fff;
      padding: 14px 30px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 16px;
      font-weight: 500;
      width: fit-content;
      margin-left: auto;
      margin-right: auto;
      transition: background 0.3s;
    }

    .checkout-btn:hover {
      background: #333;
    }

    .empty {
      text-align: center;
      color: #999;
      font-size: 18px;
      padding: 40px 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .cart-item {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }

      .cart-item img {
        width: 100%;
        height: auto;
      }

      .item-details {
        align-items: center;
      }

      .total {
        text-align: center;
      }
    }
  </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="cart-container">
  <h2>Your Cart</h2>

  <?php
  $total = 0;
  if (!empty($_SESSION['cart'])) {
      echo "<div class='cart-items'>";
      foreach ($_SESSION['cart'] as $id => $qty) {
          $res = $conn->query("SELECT * FROM products WHERE id=$id");
          if ($res->num_rows > 0) {
              $product = $res->fetch_assoc();
              $subtotal = $qty * $product['price'];
              $total += $subtotal;

              echo "<div class='cart-item'>
                      <img src='uploads/{$product['image']}' alt='{$product['name']}'>
                      <div class='item-details'>
                          <h3>{$product['name']}</h3>
                          <p>Quantity: $qty</p>
                          <p>Price: ₹{$product['price']}</p>
                          <p><strong>Subtotal:</strong> ₹$subtotal</p>
                      </div>
                    </div>";
          }
      }
      echo "</div>";
      echo "<p class='total'>Total: ₹$total</p>";
      echo "<a class='checkout-btn' href='checkout.php'>Proceed to Checkout</a>";
  } else {
      echo "<p class='empty'>🛒 Your cart is currently empty.</p>";
  }
  ?>
</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>
