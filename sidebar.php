<?php
// sidebar.php
?>
<style>
    .sidebar {
        width: 60px;
        background-color: #2c3e50;
        padding: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100vh;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
        position: fixed;
        left: 0;
        top: 0;
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
</style>

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
