<?php
// stock_management.php
include 'database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already active
}

// Fetch all products
$query = "SELECT id, sku, name, price, sale_price, image FROM products";
$result = $conn->query($query);

// Handle product deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM products WHERE id = $delete_id");
    $_SESSION['success'] = "Product deleted successfully.";
    header("Location: stock_management.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock Management</title>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            width: 60px;
            background-color: #2c3e50;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
        }
        .menu-item {
            color: white;
            text-decoration: none;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 10px 0;
            transition: background 0.3s;
            border-radius: 5px;
        }
        .menu-item:hover {
            background-color: #34495e;
        }
        .menu-item span {
            font-size: 12px;
            margin-top: 5px;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .edit-icon, .delete-icon {
            cursor: pointer;
            padding: 5px;
        }
        .delete-icon {
            color: red;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="index.php" class="menu-item" title="Home">
            🏠<span>Home</span>
        </a>
        <a href="inventory.php" class="menu-item" title="Add Products">
            ➕<span>Add</span>
        </a>
        <a href="stock_management.php" class="menu-item" title="Manage Products">
            📦<span>Manage</span>
        </a>
    </div>
    <div class="content">
        <h2>Stock Management</h2>
        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Sale Price</th>
                    <th>Product Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['sale_price']; ?></td>
                        <td><img src="<?php echo $row['image']; ?>" width="50"></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="edit-icon">✏️</a>
                            <a href="stock_management.php?delete_id=<?php echo $row['id']; ?>" class="delete-icon" onclick="return confirm('Are you sure?');">🗑️</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
