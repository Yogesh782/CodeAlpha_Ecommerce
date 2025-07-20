<?php
include 'includes/db.php';
include 'includes/navbar.php';
$id = $_GET['id'];
$res = $conn->query("SELECT * FROM products WHERE id=$id");
$product = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $product['name']; ?> - CodeAlpha Store</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: #f0f2f5;
    }

    .product-wrapper {
      max-width: 1100px;
      margin: 50px auto;
      display: flex;
      gap: 40px;
      background: #fff;
      padding: 40px;
      border-radius: 14px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
      align-items: flex-start;
    }

    .product-wrapper img {
      width: 400px;
      height: 550px;
      object-fit: cover;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .product-details {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .product-details h2 {
      font-size: 32px;
      margin-bottom: 16px;
      color: #222;
    }

    .product-details p {
      font-size: 16px;
      color: #555;
      line-height: 1.6;
    }

    .price {
      font-size: 26px;
      font-weight: bold;
      color: #27ae60;
      margin-top: 20px;
    }

    form {
      margin-top: 30px;
    }

    form label {
      font-size: 15px;
      font-weight: 500;
      margin-right: 10px;
    }

    input[type="number"] {
      padding: 10px;
      width: 80px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-right: 20px;
      outline: none;
      transition: border-color 0.3s ease;
    }

    input[type="number"]:focus {
      border-color: #2ebf91;
    }

    button[type="submit"] {
      padding: 12px 28px;
      background: #1e1e2f;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    button[type="submit"]:hover {
      background: #2c2c3f;
      transform: translateY(-2px);
    }

    @media (max-width: 768px) {
      .product-wrapper {
        flex-direction: column;
        padding: 20px;
        text-align: center;
      }

      .product-wrapper img {
        width: 100%;
        height: auto;
      }

      .product-details {
        align-items: center;
      }

      form {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
      }

      input[type="number"] {
        margin-right: 0;
      }
    }
  </style>
</head>
<body>

<div class="product-wrapper">
  <img src="uploads/<?= $product['image']; ?>" alt="<?= $product['name']; ?>">
  <div class="product-details">
    <div>
      <h2><?= $product['name']; ?></h2>
      <p><?= $product['description']; ?></p>
      <p class="price">₹<?= $product['price']; ?></p>
    </div>

    <form method="POST" action="cart.php">
      <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
      <label for="qty">Quantity:</label>
      <input type="number" id="qty" name="quantity" value="1" min="1">
      <button type="submit" name="add_to_cart">Add to Cart</button>
    </form>
  </div>
</div>

</body>
</html>
