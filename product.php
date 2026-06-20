<?php
session_start();
include 'db.php';
$product_id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("<h2 style='text-align:center;color:#f44336;margin-top:50px;'>المنتج غير موجود</h2>");
}
$product = $result->fetch_assoc();
$images_array = explode(',', $product["image_url"]);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['title']; ?></title>
    <style>
        .product-details {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .product-images img{
            width: 300px;
            max-width: 100%;
            height: auto;
            margin: 5px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .price {
            color: #28a745;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }
        .desc{
            color: #444;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
            margin: 20px 0;
        }
        </style>
</head>
<body>
    <div class="product-details">
        <h1><?php echo $product['title']; ?></h1>
        <div class="product-images">
            <?php foreach ($images_array as $img): ?>
                <? if (!empty($img)): ?>
                    <img src="<?php echo trim($img); ?>" alt="صورة المنتج: <?php echo $product['title']; ?>">
                <? endif; ?>
            <?php endforeach; ?>
        </div>
        <p class="desc"><?php echo nl2br($product['description']); ?></p>
        <p class="price"><?php echo $product['price']; ?> ل.س</p>
        <form method="POST" action="add_to_cart.php">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <button type="submit" style="background-color: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">أضف إلى السلة</button>
    </div>
</body>
</html>

