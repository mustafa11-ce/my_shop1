<?php 
include 'db.php';
session_start();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>متجر إلكتروني</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .product-grid{
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .product-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            width: 250px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .product-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }
        .price {
            color: #27ae60;
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }
        .add-to-cart {
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .add-to-cart:hover {
            background-color: #1a5276;
        }
    </style>
</head>
<body>
    <div style="background-color: #333; padding: 15px 20px; display: flex; justify-content:space-between; align-items: center; direction: rtl;margin-bottom: 20px;">
    <div> <a href="cart.php" style="color:#4caf50;text-decoration:none;margin-left: 20px;font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-weight:bold;">سلة المشتريات</a></div>
    <?php if (isset($_SESSION['user_id'])) : ?>
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="profile.php" style="color: white; text-decoration:none;font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif">
            <?php $user_img = !empty($_SESSION['user_image']) ? $_SESSION['user_image'] : 'default-profile.png'; ?>
            <img src="uploads/<?php echo $user_img; ?>" alt="profile" style="width: 35px; height: 35px; border-radius: 50%;border: 1px solid #4caf50 ;
            object-fit: cover; vertical-align: middle;">حسابي</a>
            <a href="logout.php" style="color: #ff4d4d; text-decoration:none;font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
            ;font-size:13px;>تسجيل خروج</a>
        </div>
    <?php  else : ?>
        <a href="login.php" style="color:white;text-decoration:none;margin-left:15px;font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
        ;">تسجيل الدخول</a>
        <a href="register.php" style="color: white; text-decoration:none;border: 1px solid white; padding: 5px 10px; border-radius: 5px;
        font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
        >تسجيل حساب جديد</a>
    <?php endif; ?>

    </div>
    <h1 style="text-align: center;">أحدث المنتجات</h1>
    <div class="product-grid">
        <?php
        $sql = "SELECT * FROM products where stock >0 ORDER BY id DESC";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        echo '<div class="product-card">';
        echo '<a href="product.php?id=' . $row["id"] . '" style="text-decoration:none; color:inherit;">';
        $db_image = $row["image_url"] ?? '';
        $images_array = explode(',', $db_image);
        $first_image = trim($images_array[0]);
        if (!empty($first_image) && file_exists($first_image)) {
            echo '<img src="' . $first_image . '" alt="' . $row["title"] . '" class="product-img" style="width:100%; height:150px; object-fit:cover;">';
        } else {
            echo '<img src="uploadssalin/default.jpg" alt="شعار المحل" class="product-img" style="width:100%; height:150px; object-fit:contain;">';
        }
        echo '<h3>' . $row["title"] . '</h3>';
        echo '</a>'; 
        $short_desc = mb_substr($row["description"], 0, 50) . '...';
        echo '<p style="color: #666; font-size: 14px; margin: 10px 0;">' . $short_desc . '</p>';
        echo '<p class="price">' . $row["price"] . ' ل.س</p>';
        echo '<form method="POST" action="add_to_cart.php" style="margin-top: 10px; padding: 0;">';
        echo '<input type="hidden" name="product_id" value="' . $row["id"] . '">';
        echo '<button type="submit" class="add-to-cart">أضف إلى السلة</button>';
        echo '</form>';
        
        echo '</div>';
    }
            }  else {
            echo "<p style='text-align: center;'>لا توجد منتجات متاحة حالياً.</p>";
        }   
        
        $conn->close();
        ?>
    </div>
    </div>
    <script>
        function addTocart(productId) {
            alert("تم إضافة المنتج إلى السلة! (معرف المنتج: " + productId + ")");
        }
        fetch('http://localhost/my_shop/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ productId: productId })
        }).then(response => response.text())
            .then(data => console.log(data))
            .catch(error => console.error('Error:', error));
    </script>
</body>
</html>