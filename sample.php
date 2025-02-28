<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Inventory System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f8f8f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
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

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 5px;
            outline: none;
            transition: 0.3s;
        }

        .form-group label {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            font-size: 14px;
            color: #aaa;
            pointer-events: none;
            transition: 0.3s;
        }

        .form-group input:focus,
        .form-group input:valid {
            border-color: #ff2a6d;
        }

        .form-group input:focus + label,
        .form-group input:valid + label {
            top: 8px;
            font-size: 12px;
            color: #ff2a6d;
        }

        .file-input {
            text-align: left;
            font-size: 14px;
        }

        .preview {
            margin-top: 10px;
            display: none;
            text-align: center;
        }

        .preview img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            margin-top: 10px;
        }

        button {
            width: 100%;
            background-color: #ff2a6d;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #e0245e;
        }

        .footer {
            margin-top: 15px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Add Product</h2>
        <form>
            <div class="form-group">
                <input type="text" id="sku" required>
                <label for="sku">SKU</label>
            </div>

            <div class="form-group">
                <input type="text" id="name" required>
                <label for="name">Name</label>
            </div>

            <div class="form-group">
                <input type="number" id="price" required>
                <label for="price">Price</label>
            </div>

            <div class="form-group">
                <input type="number" id="salePrice">
                <label for="salePrice">Sale Price</label>
            </div>

            <div class="file-input">
                <label for="image">Product Image:</label>
                <input type="file" id="image" accept="image/*" onchange="previewImage()">
                <div class="preview">
                    <img id="imagePreview">
                </div>
            </div>

            <button type="submit">Add Product</button>
        </form>

        <div class="footer">
            &copy; 2025 Melvenko Designs. All rights reserved.
        </div>
    </div>

    <script>
        function previewImage() {
            const file = document.getElementById("image").files[0];
            const preview = document.querySelector(".preview");
            const img = document.getElementById("imagePreview");

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.style.display = "block";
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = "none";
            }
        }
    </script>

</body>
</html>
