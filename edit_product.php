<?php
// edit_product.php
include 'database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Invalid product ID.";
    header("Location: stock_management.php");
    exit;
}

$product_id = $_GET['id'];
$result = $conn->query("SELECT * FROM products WHERE id = $product_id");
$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $sale_price = $_POST['sale_price'];
    
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/';
        $imagePath = $uploadDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    } else {
        $imagePath = $product['image'];
    }

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, sale_price=?, image=? WHERE id=?");
    $stmt->bind_param("sddsi", $name, $price, $sale_price, $imagePath, $product_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Product updated successfully.";
    header("Location: stock_management.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f8f8;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 700px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        input {
            width: calc(100% - 20px);
            padding: 12px;
            margin-bottom: 10px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 5px;
            outline: none;
            transition: 0.3s;
        }
        input:focus, input:valid {
            border-color: #ff0055;
        }
        button {
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            background-color: #ff0055;
            color: white;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }
        button:hover {
            background-color: #cc0044;
        }
        .image-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .image-container label {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .image-container img {
            max-width: 100%;
            border-radius: 5px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .back-link {
            display: inline-block;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Edit Product</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Product Name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br>
                <input type="number" step="0.01" name="price" placeholder="Price" value="<?php echo $product['price']; ?>" required><br>
                <input type="number" step="0.01" name="sale_price" placeholder="Sale Price" value="<?php echo $product['sale_price']; ?>"><br>
                <input type="file" name="image"><br>
                <button type="submit">Update Product</button>
            </form>
            <a href="stock_management.php" class="back-link">Back to Stock Management</a>
        </div>
        <div class="image-container">
            <label>Product Image</label>
            <img src="<?php echo $product['image']; ?>" alt="Product Image">
        </div>
    </div>
</body>
</html>
