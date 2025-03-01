<?php
// inventory.php
include 'database.php';
include 'sidebar.php'; // Include the sidebar

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
        header("Location: inventory.php");
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
        header("Location: inventory.php");
        exit;
    } else {
        $stmt->close();
        
        // Insert the new product
        $stmt = $conn->prepare("INSERT INTO products (sku, name, price, sale_price, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdds", $sku, $name, $price, $sale_price, $imagePath);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['success'] = "Product successfully added.";
        header("Location: inventory.php");
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
            display: flex;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 200px; /* Adjust for sidebar */
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: left;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            max-width: 400px;
        }
        input, button {
            width: 100%;
            margin: 5px 0;
        }
        input {
            padding: 12px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 5px;
            outline: none;
        }
        button {
            padding: 10px;
            border-radius: 10px;
            background-color: #ff0055;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #cc0044;
        }
        .order-button {
            width: auto; /* Change from 100% to auto */
            display: block; /* Ensures it doesn't stretch */
            margin-top: 10px; /* Adjust spacing */
            padding: 10px;
            border-radius: 10px;
            background-color: #ff0055;
            color: white;
            cursor: pointer;
        }

    </style>
</head>
<body>
    <div class="content">
        <h2>Add New Product</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="sku" placeholder="SKU" required>
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="price" placeholder="Price" required>
            <input type="text" name="sale_price" placeholder="Sale Price">
            <input type="file" name="image" required>
            <button type="submit" name="add_product">Add Product</button>
        </form>
        
        <!-- Place Order Button -->
        <button onclick="openModal()" class="order-button">Place Order</button>
    </div>
    
    <script>
        function openModal() {
            alert("Place Order functionality triggered. Implement order logic here.");
            // You can add further logic to show a modal or process an order
        }
    </script>
</body>
</html>
