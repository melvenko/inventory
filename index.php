<?php
// index.php
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

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imagePath = $uploadDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    // Ensure price and sale_price are cast as floats if needed
    $price = (float)$price;
    $sale_price = (float)$sale_price;

    $stmt = $conn->prepare("INSERT INTO products (sku, name, price, sale_price, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdds", $sku, $name, $price, $sale_price, $imagePath);
    $stmt->execute();
    $stmt->close();
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
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label, input {
            display: block;
            width: 60%;
            margin: 5px 0;
            text-align: left;
        }
        input {
            padding: 12px 20px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 20%;
            padding: 12px 20px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 24px;
            width: 60%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            text-align: left;
            z-index: 2;
        }
        .modal.active {
            display: block;
        }
        .modal button {
            margin-top: 10px;
            width: 20%;
        }
        .modal .close-btn {
            float: left;
            margin-bottom: 10px;
        }
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1;
        }
    </style>
    <script>
        function openModal() {
            document.getElementById('orderModal').classList.add('active');
            document.getElementById('modalOverlay').style.display = 'block';
        }
        function closeModal() {
            document.getElementById('orderModal').classList.remove('active');
            document.getElementById('modalOverlay').style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Add Product</h2>
        <table>
            <tr>
                <td>
                    <form method="post" enctype="multipart/form-data">
                        <label>SKU:</label> <input type="text" name="sku" placeholder="SKU" required>
                        <label>Name:</label> <input type="text" name="name" placeholder="Name" required>
                        <label>Price:</label> <input type="text" name="price" placeholder="Price" required>
                        <label>Sale Price:</label> <input type="text" name="sale_price" placeholder="Sale Price">
                        <label>Image:</label> <input type="file" name="image">
                        <button type="submit" name="add_product">Add Product</button>
                    </form>
                </td>
            </tr>
        </table>

        <!-- Open Modal Link -->
        <h2><a href="javascript:void(0)" onclick="openModal()">Place Order</a></h2>
    </div>

    <!-- Overlay -->
    <div id="modalOverlay" class="modal-overlay" onclick="closeModal()"></div>

    <!-- Modal -->
    <div id="orderModal" class="modal">
        <h2>Place Order</h2>
        <table>
            <tr>
                <td>
                    <form method="post">
                        <label>Product ID:</label> <input type="text" name="product_id" placeholder="Product ID" required>
                        <label>Quantity:</label> <input type="text" name="quantity" placeholder="Quantity" required>
                        <button type="submit" name="place_order">Place Order</button>
                    </form>
                </td>
            </tr>
        </table>
        <button class="close-btn" onclick="closeModal()">Close</button>
    </div>
</body>
</html>
