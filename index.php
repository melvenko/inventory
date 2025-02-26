<?php
// index.php
include 'database.php';

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

    $stmt = $conn->prepare("INSERT INTO products (sku, name, price, sale_price, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdds", $sku, $name, $price, $sale_price, $imagePath);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $stmt = $conn->prepare("INSERT INTO orders (product_id, quantity) VALUES (?, ?)");
    $stmt->bind_param("ii", $product_id, $quantity);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
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
            width: 60%; /* Reduced from 70% */
            margin: 5px 0;
            text-align: left;
        }
        input {
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 20%; /* Reduced button width - feb 26*/
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
            padding: 24px; /* Increased modal size */
            width: 60%; /* Increased modal size by 20% */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
        .modal.active {
            display: block;
            align-items: left;
        }
        .modal button {
            margin-top: 10px;
            width: 20%; /* Reduced button width inside modal */
        }
    </style>
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
                        <label>Sale Price:</label> <input type="text" placeholder="Sale Price" name="sale_price">
                        <label>Image:</label> <input type="file" name="image">
                        <button type="submit" name="add_product">Add Product</button>
                    </form>
                </td>
            </tr>
        </table>

        <h2><a href="#" onclick="document.getElementById('orderModal').classList.add('active')">Place Order</a></h2>
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
            <button onclick="document.getElementById('orderModal').classList.remove('active')">Close</button>
        </div>
    </div>
</body>
</html>

