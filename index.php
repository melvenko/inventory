<?php
// Start session for transient messages
session_start();

include 'database.php';

// Enable error reporting for debugging (remove in production)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $sku = $_POST['sku'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $sale_price = $_POST['sale_price'];
    $imagePath = '';

    // Check if product with same SKU already exists
    $stmt = $conn->prepare("SELECT id FROM products WHERE sku = ?");
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "You're trying to create a duplicate product. Enter a new one.";
        header("Location: index.php");
        exit;
    }
    $stmt->close();

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imagePath = $uploadDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    // Ensure price and sale_price are cast as floats
    $price = (float)$price;
    $sale_price = (float)$sale_price;

    $stmt = $conn->prepare("INSERT INTO products (sku, name, price, sale_price, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdds", $sku, $name, $price, $sale_price, $imagePath);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Product added successfully!";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $stmt = $conn->prepare("INSERT INTO orders (product_id, quantity) VALUES (?, ?)");
    $stmt->bind_param("ii", $product_id, $quantity);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Order placed successfully!";
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventory Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            text-align: center;
        }
        .container {
            width: 60%;
            margin: auto;
        }
        .message {
            padding: 10px;
            margin: 10px auto;
            width: 80%;
            border-radius: 5px;
            font-weight: bold;
            display: none;
        }
        .error {
            background-color: #ffcccc;
            border: 1px solid #ff0000;
            color: #990000;
        }
        .success {
            background-color: #ccffcc;
            border: 1px solid #008000;
            color: #006600;
        }
    </style>
    <script>
        function closeMessage() {
            document.getElementById('messageBox').style.display = 'none';
        }
        window.onload = function() {
            var messageBox = document.getElementById('messageBox');
            if (messageBox) {
                messageBox.style.display = 'block';
                setTimeout(closeMessage, 5000); // Auto-hide after 5 sec
            }
        };
    </script>
</head>
<body>
    <div class="container">
        <!-- Display Transient Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div id="messageBox" class="message error" onclick="closeMessage()">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php elseif (isset($_SESSION['success'])): ?>
            <div id="messageBox" class="message success" onclick="closeMessage()">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <h2>Add Product</h2>
        <form method="post" enctype="multipart/form-data">
            <label>SKU:</label> <input type="text" name="sku" placeholder="SKU" required>
            <label>Name:</label> <input type="text" name="name" placeholder="Name" required>
            <label>Price:</label> <input type="text" name="price" placeholder="Price" required>
            <label>Sale Price:</label> <input type="text" name="sale_price" placeholder="Sale Price">
            <label>Image:</label> <input type="file" name="image">
            <button type="submit" name="add_product">Add Product</button>
        </form>

        <h2><a href="javascript:void(0)" onclick="openModal()">Place Order</a></h2>
    </div>

    <!-- Overlay -->
    <div id="modalOverlay" class="modal-overlay" onclick="closeModal()"></div>

    <!-- Modal -->
    <div id="orderModal" class="modal">
        <h2>Place Order</h2>
        <form method="post">
            <label>Product ID:</label> <input type="text" name="product_id" placeholder="Product ID" required>
            <label>Quantity:</label> <input type="text" name="quantity" placeholder="Quantity" required>
            <button type="submit" name="place_order">Place Order</button>
        </form>
        <button class="close-btn" onclick="closeModal()">Close</button>
    </div>
</body>
</html>
