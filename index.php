<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
        }
        .container {
            width: 80%;
            max-width: 1200px;
            text-align: center;
        }
        h1 {
            color: #333;
            font-size: 36px;
        }
        p {
            color: #555;
            font-size: 18px;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }
        .left-column img {
            width: 100%;
            border-radius: 10px;
        }
        .right-column {
            background: linear-gradient(135deg, #ffffff, #f0f0f0);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 300px;
        }
        .right-column p {
            font-size: 16px;
            color: #444;
            line-height: 1.5;
            font-weight: 500;
        }
        .badge {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            margin-top: 50px;
            padding: 20px;
            background-color: #333;
            color: white;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .footer-logo {
            height: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Effortless Inventory Management</h1>
        <p>Optimize your inventory tracking and streamline operations with precision and ease.</p>
        
        <div class="grid">
            <div class="left-column">
                <a href="inventory.php">
                    <img src="images/vecteezy.jpg" alt="Inventory System Illustration">
                </a>
            </div>
            <div class="right-column">
                <p>Efficient stock management prevents shortages and overstocking. An inventory system automates tracking, reduces errors, and ensures smooth operations. This leads to better decision-making, increased efficiency, and improved customer satisfaction.</p>
            </div>
        </div>
        
        <div class="badge">
            <a href="https://app.instawp.io/register?ref=fnK4lgnFE4" target="_blank">
                <img src="images/referral-badge-1.svg" alt="Referral Badge">
            </a>
        </div>
    </div>
    
    <footer>
        <img src="images/get.png" alt="Melvenko Designs Logo" class="footer-logo">
        <span>&copy; 2025 Melvenko Designs. All rights reserved.</span>
    </footer>
</body>
</html>
