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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            display: flex;
            flex-direction: column;
            background-color: #f8f8f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            /* width: 60%;
            margin: auto;
            flex-grow: 1; */
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 15px;
            color: #333;
        }
        

        form {
            display: flex;
            flex-direction: column;
            align-items: center;

            position: relative;
            margin-bottom: 20px;
        }
        label, input, button {
            width: 100%;
            max-width: 400px;
            margin: 5px 0;
        }
        input {
            padding: 10px;
            /* border: 1px solid #ccc; */
            background: #f5f5f5;
            /* border-radius: 4px; */
            box-sizing: border-box;

            /*width: 100%; */
            padding: 12px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 5px;
            outline: none;
            transition: 0.3s;
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
        <nav style="text-align: center; margin-bottom: 20px;">
            <a href="index.php" style="text-decoration: none; font-size: 18px; font-weight: bold; color: #ff0055;">Home</a>
        </nav>
        <div id="error-message" class="error-message">
            <?php if (isset($_SESSION['error'])) { echo $_SESSION['error']; unset($_SESSION['error']); } ?>
        </div>
        <div id="success-message" class="success-message">
            <?php if (isset($_SESSION['success'])) { echo $_SESSION['success']; unset($_SESSION['success']); } ?>
        </div>
        <h2>Add New Product</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="sku" placeholder="SKU" required>
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="price" placeholder="Price" required>
            <input type="text" name="sale_price" placeholder="Sale Price">
            <input type="file" name="image">
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
        <h2>New Order</h2>
        <p>Select a product and quantity to place an order.</p>
        <table>
            <tr>
                <td>
                    <form method="post">
                        <label>Enter Product ID:</label> <input type="text" id="product_id" name="product_id" placeholder="Product ID" required>
                        <label>Enter Quantity:</label> <input type="number" id="quantity" name="quantity" placeholder="Quantity" required>
                        <button type="submit" name="place_order">Submit Order</button>
                    </form>
                </td>
            </tr>
        </table>
        <!-- <button class="close-btn" onclick="closeModal()">Cancel</button> -->
        <button class="close-btn" onclick="closeModal()">x</button>

    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
