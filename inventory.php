<?php
// index.php
include 'database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already active
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $sku = $_POST['sku'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $sale_price = $_POST['sale_price'];
    $imagePath = '';

    if (empty($_FILES['image']['name'])) {
        $_SESSION['error'] = "Add Product Image.";
        header("Location: index.php");
        exit;
    }

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $imagePath = $uploadDir . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);

    // Check if the SKU already exists
    $stmt = $conn->prepare("SELECT id FROM products WHERE sku = ?");
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Product already exists. Create a new one.";
        $stmt->close();
        header("Location: index.php");
        exit;
    } else {
        $stmt->close();
        
        // Insert the new product
        $stmt = $conn->prepare("INSERT INTO products (sku, name, price, sale_price, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdds", $sku, $name, $price, $sale_price, $imagePath);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['success'] = "Product successfully added.";
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventory Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            width: 60%;
            margin: auto;
            flex-grow: 1;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label, input, button {
            width: 100%;
            max-width: 400px;
            margin: 5px 0;
        }
        input {
            padding: 10px;
            border: 1px solid #ccc;
            background: #f5f5f5;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #ff0055;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
            color: white;
            cursor: pointer;
        }
        .badge {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #fff;
            padding: 10px 0;
            text-align: center;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .footer-logo {
            width: 50px;
            height: auto;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav style="text-align: center; margin-bottom: 20px;">
            <a href="index.php" style="text-decoration: none; font-size: 18px; font-weight: bold; color: #ff0055;">Home</a>
        </nav>
        <div id="error-message" class="error-message">
            <?php if (isset($_SESSION['error'])) { echo $_SESSION['error']; unset($_SESSION['error']); } ?>
        </div>
        <div id="success-message" class="success-message">
            <?php if (isset($_SESSION['success'])) { echo $_SESSION['success']; unset($_SESSION['success']); } ?>
        </div>
        <h2>Add Product</h2>
        <form method="post" enctype="multipart/form-data">
            <label>SKU:</label> <input type="text" name="sku" placeholder="SKU" required>
            <label>Name:</label> <input type="text" name="name" placeholder="Name" required>
            <label>Price:</label> <input type="text" name="price" placeholder="Price" required>
            <label>Sale Price:</label> <input type="text" name="sale_price" placeholder="Sale Price">
            <label>Image:</label> <input type="file" name="image">
            <button type="submit" name="add_product">Add Product</button>
        </form>
        <div class="badge">
            <a href="https://app.instawp.io/register?ref=fnK4lgnFE4" target="_blank">
                <img src="images/referral-badge-1.svg" alt="Referral Badge">
            </a>
        </div>
    </div>
    <footer class="footer">
        <img src="images/get.png" alt="Melvenko Designs Logo" class="footer-logo">
        <span>&copy; 2025 Melvenko Designs. All rights reserved.</span>
    </footer>
</body>
</html>
