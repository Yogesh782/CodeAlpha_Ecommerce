<?php
session_start();
include 'includes/db.php';
include 'includes/navbar.php';

// Handle category filter
$filter = '';
if (isset($_GET['category']) && $_GET['category'] !== '') {
    $category = $_GET['category'];
    $filter = "WHERE category = '$category'";
}

// Handle search
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $searchTerm = $_GET['search'];
    $filter = "WHERE name LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%'";
}

$query = "SELECT * FROM products $filter";
$res = $conn->query($query);

// Fetch all categories
$categories = $conn->query("SELECT DISTINCT category FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CodeAlpha Clothing Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f7f7f7;
        }
       .hero {
    background: url('https://images.unsplash.com/photo-1618354691462-f6b9c27ec33b?auto=format&fit=crop&w=1500&q=80') no-repeat center center/cover;
    height: 350px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    color: white;
    text-shadow: 2px 2px 5px #000;
}

        .hero h1 {
            font-size: 48px;
            margin: 0;
        }
        .hero p {
            font-size: 20px;
            margin-top: 10px;
        }
        .filter-bar {
            background: #f0f0f0;
            padding: 15px;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-bar input, .filter-bar select, .filter-bar button {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        /* Product Container Grid */
.product-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 24px;
  padding: 30px;
}

/* Individual Product Card */
.product {
  background: #ffffff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  text-align: center;
  padding: 16px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
}

/* Product Image */
.product img {
  width: 100%;
  height: 300px; /* Portrait style */
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 12px;
}

/* Product Name */
.product h3 {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  margin: 8px 0;
}

/* Product Price */
.product p {
  font-size: 16px;
  font-weight: 600;
  color: #2ebf91;
  margin-bottom: 12px;
}

/* View Details Button */
.product a {
  display: inline-block;
  background-color: #1e90ff;
  color: #fff;
  padding: 10px 18px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
  transition: background 0.3s ease, transform 0.2s ease;
}

.product a:hover {
  background-color: #187bcd;
  transform: scale(1.05);
}

        .footer {
            background: #222;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<div class="hero">
    <h1>Clothing for Everyone</h1>
    <p>Trendy fashion at affordable prices</p>
</div>

<!-- Filter/Search Bar -->
<div class="filter-bar">
    <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
        <input type="text" name="search" placeholder="Search..." value="<?= $_GET['search'] ?? '' ?>">
        <select name="category">
            <option value="">All Categories</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['category']; ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat['category']) ? 'selected' : '' ?>>
                    <?= $cat['category']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Filter</button>
    </form>
</div>

<!-- Product List -->
<div class="product-container">
<?php
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        echo "<div class='product'>
                <img src='uploads/{$row['image']}' alt='{$row['name']}'>
                <h3>{$row['name']}</h3>
                <p>₹{$row['price']}</p>
                <a href='product.php?id={$row['id']}'>View Details</a>
              </div>";
    }
} else {
    echo "<p style='padding:20px;'>No products found.</p>";
}
?>
</div>
</body>
</html>
<?php include 'includes/footer.php'; ?>
