<?php
// index.php
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
            background: #f5f5f5;
            box-sizing: border-box;
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
            transition: background 0.3s ease;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #cc0044;
        }

        .order-button {
            background-color: #ff0055;
            color: white;
            padding: 12px 20px;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .order-button:hover {
            background-color: #cc0044;
        }

        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 20px;
            padding: 24px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            text-align: center;
            z-index: 2;
        }
        .modal.active {
            display: block;
        }

        .modal .close-btn {
            position: absolute;
            top: 0px;
            right: -170px;
            background: none;
            border: none;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: black;
            transition: color 0.3s ease;
        }
        .modal .close-btn:hover {
            color: red;
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
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        .modal form input,
        .modal form button {
            width: calc(100% - 20px);
            max-width: 400px;

            /* width: 90%;
            max-width: 300px; Keeps form elements at a readable size */
            text-align: center;
        }
        .modal form input {
            padding: 12px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 5px;
            outline: none;
            
            transition: 0.3s;
        }
        .modal form button {
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            background-color: #ff0055;
            color: white;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .modal form button:hover {
            background-color: #cc0044;
        }
        
        .form-group input:focus,
        .form-group input:valid {
            border-color: #ff2a6d;
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
        document.addEventListener("DOMContentLoaded", function() {
            var errorMessage = document.getElementById("error-message");
            var successMessage = document.getElementById("success-message");

            if (errorMessage && errorMessage.innerText.trim() !== "") {
                errorMessage.style.display = "block";
                setTimeout(function() {
                    errorMessage.style.display = "none";
                }, 3000);
            }

            if (successMessage && successMessage.innerText.trim() !== "") {
                successMessage.style.display = "block";
                setTimeout(function() {
                    successMessage.style.display = "none";
                }, 3000);
            }
        });

    </script>

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
        <div id="error-message" class="error-message" style="display: none;">
                <?php 
                    if (isset($_SESSION['error'])) { 
                        echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var errorMessage = document.getElementById('error-message');
                                    errorMessage.innerText = '" . $_SESSION['error'] . "';
                                    errorMessage.style.display = 'block';
                                    setTimeout(function() {
                                        errorMessage.style.display = 'none';
                                    }, 3000);
                                });
                            </script>";
                        unset($_SESSION['error']); 
                    } 
                ?>
            </div>

            <div id="success-message" class="success-message" style="display: none;">
                <?php 
                    if (isset($_SESSION['success'])) { 
                        echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var successMessage = document.getElementById('success-message');
                                    successMessage.innerText = '" . $_SESSION['success'] . "';
                                    successMessage.style.display = 'block';
                                    setTimeout(function() {
                                        successMessage.style.display = 'none';
                                    }, 3000);
                                });
                            </script>";
                        unset($_SESSION['success']); 
                    } 
                ?>
            </div>


        <h2>Add New Product</h2>
        <form method="post" enctype="multipart/form-data" class="form-group">
            <input type="text" name="sku" placeholder="SKU" required>
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="price" placeholder="Price" required>
            <input type="text" name="sale_price" placeholder="Sale Price">
            <input type="file" name="image" required>
            <button type="submit" name="add_product">Add Product</button>
        </form>
        </td>
            </tr>
        </table>

        <!-- Open Modal Link -->
        <button onclick="openModal()" class="order-button">Place Order</button>

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
                        <input type="text" id="product_id" name="product_id" placeholder="Enter Product ID" required>
                        <input type="number" id="quantity" name="quantity" placeholder="Enter Quantity" required>
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
