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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
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
    <a href="index.php">Back to Home</a>
</body>
</html>
