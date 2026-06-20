<?php
session_start();
include 'db.php';
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])){
    $remove_id = $_GET['id'];
    if (isset($_SESSION['cart'][$remove_id])){
        unset($_SESSION['cart'][$remove_id]);
    }
    header("location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>سلة المشتريات</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 20px;
            }
            .navbar {
                background-color: #333;
                color: white;
                padding: 10px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .navbar a {
                color: white;
                text-decoration: none;
                margin-left: 15px;
            }
            .cart-container {
                max-width: 800px;
                margin:  auto;
                background-color: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            /* h1 {
                text-align: center;
                color: #333;
            } */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                padding: 10px;
                text-align: right;
                border-bottom: 1px solid #ddd;
            }
            th {
                background-color: #f8f8f8;
            }
            .total-box {
                text-align: left;
                font-size: 18px;
                font-weight: bold;
                color: #e74c3c;
                margin-top: 20px;
            }
            .checkout-btn{
                display: block;
                width: 100%;
                padding: 10px 20px;
                background-color: #27ae60;
                color: white;
                text-align: center;
                text-decoration: none;
                border-radius: 5px;
                margin-top: 20px;
                float: left;
            }
            .checkout-btn:hover {
                background-color: #2ecc71;
            }
            .empty-cart {
                text-align: center;
                font-size: 18px;
                color: #7f8c8d;
                margin-top: 20px;
            }
            .return-btn {
                display: inline-block;
                padding: 10px 20px;
                background-color: #3498db;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="navbar">
            <a href="index.php">الصفحة الرئيسية</a>
            <!-- <a href="cart.php" class="active"> سلة المشتريات</a> -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="checkout.php">إتمام الطلب</a>
            <?php else: ?>
                <a href="login.php">تسجيل الدخول</a>
                <a href="register.php">إنشاء حساب</a>
            <?php endif; ?>
        </div>
        <div class="cart-container">
            <h1>سلة المشتريات</h1>
            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): 
                echo "<table>";
                echo "<tr>
                        <th>المنتج</th>
                        <th>السعر</th>
                        <th>الكمية</th>
                        <th>الإجمالي</th>
                        <th>حذف</th>
                    </tr>";
                    $total_price = 0;
                    $product_ids = implode(',', array_keys($_SESSION['cart']));
                    if (!empty($product_ids)) {
                        $sql = "SELECT id, title, price FROM products WHERE id IN ($product_ids)";
                        $result = $conn->prepare($sql);
                        $result->execute();
                        $result = $result->get_result();
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $id = $row['id'];
                                $quantity = $_SESSION['cart'][$id];
                                $subtotal = $row['price'] * $quantity;
                                $total_price += $subtotal;
                    echo "<tr>";
                        echo "<td> {$row['title']}</td>";
                        echo "<td> {$row['price']} ل.س</td>";
                        echo "<td><b> $quantity</b> </td>";
                        echo "<td> {$subtotal} ل.س</td>";
                        echo "<td style='text-align=center'><a href='cart.php?action=remove&id={$id}'style='background-color:#ff4d4d;color:white;padding: 5px 10px;
                        text-decoration:none;border-radius:4px;font-size: 13px;'>إزالة </a> </td>";
                    echo "</tr>";
                            }
                        }
                    
                    echo "</table>";
                    $_SESSION['total_price'] = $total_price;
                    echo "<div class='total-box'>المجموع: {$total_price} ل.س</div>";
                    echo "<a href='checkout.php' class='checkout-btn'>إتمام الطلب</a>";
                    } else {
                        echo "<div class='empty-cart'>سلة المشتريات فارغة.</div>";
                        echo "<div style='text-align:center; margin-top:20px;'><a href='index.php' class='return-btn'>العودة للتسوق</a></div>";
                    }?>
        </div>
            <?php endif; ?>       
            </div>
    </body>
</html>