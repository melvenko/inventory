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
        /* label, input {
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
        } */
        /* button {
            width: 20%;
            padding: 12px 20px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        } */

        button {
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            /* background-color: #28a745; */
            background-color: #ff0055;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
            color: white;
            cursor: pointer;
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
            /* display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 20px;
            padding: 24px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 2; */
            
            padding: 24px;
            width: 90%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            text-align: center;
            z-index: 2;
            border-radius: 20px;
            position: relative;
        }
        .modal.active {
            display: block;
        }
        .modal button {
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 10px;
            background-color: #ff0055;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
            color: white;
            cursor: pointer;
        }
        .modal button[type="submit"] {
            background-color: #ff0055;
            color: white;
        }

        .modal .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
/*             
            float: left;
            margin-bottom: 10px;
            background-color: #ccc;
            color: black;
            width: 50%; */
        }
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
            z-index: 1;
        }
        .modal h2 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #333;
        }
        .modal p {
            font-size: 14px;
            color: #777;
            margin-bottom: 20px;
        }
        .modal form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .modal input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .error-message, .success-message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            display: none;
        }
        .error-message {
            background-color:rgb(236, 5, 5);
            color: #fff;
        }
        .success-message {
            background-color: rgba(0, 255, 0, 0.2);
            color: green;
        }
        @media (max-width: 480px) {
            body {
                margin: 10px;
            }
            .container {
                width: 100%;
            }
            label, input, button {
                width: 100%;
            }
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

    <script>
            document.addEventListener("DOMContentLoaded", function() {
                var errorMessage = document.getElementById("error-message");
                var successMessage = document.getElementById("success-message");

                if (errorMessage.innerText.trim() !== "") {
                    errorMessage.style.display = "block";
                    setTimeout(function() {
                        errorMessage.style.display = "none";
                    }, 3000);
                }
                if (successMessage.innerText.trim() !== "") {
                successMessage.style.display = "block";
                setTimeout(function() {
                    successMessage.style.display = "none";
                }, 3000);
            }
            });
    </script>
</head>
<body>
    <div class="container">
    <div id="error-message" class="error-message">
            <?php if (isset($_SESSION['error'])) { echo $_SESSION['error']; unset($_SESSION['error']); } ?>
        </div>
        <div id="success-message" class="success-message">
            <?php if (isset($_SESSION['success'])) { echo $_SESSION['success']; unset($_SESSION['success']); } ?>
        </div>
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
        <button class="close-btn" onclick="closeModal()">Cancel</button>
    </div>
</body>
</html>
