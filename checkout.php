<?php
session_start();
include 'db.php';
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");    
    exit;
}
$user_id = $_SESSION['user_id'];
$total_price = $_SESSION['total_price'];
$status = 'قيد التجهيز';
$success_message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $user_id, $total_price, $status);
        $stmt->execute();
        $order_id = $conn->insert_id;
        $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
        $product_ids = implode(',', array_keys($_SESSION['cart']));
        $sql_prices = "SELECT id, price FROM products WHERE id IN ($product_ids)";
        $result_prices = $conn->query($sql_prices);
        $current_prices = [];
        while ($row = $result_prices->fetch_assoc()) {
            $current_prices[$row['id']] = $row['price'];
        }
        foreach ($_SESSION['cart'] as $p_id => $qty) {
            $price = $current_prices[$p_id];
            $stmt_items->bind_param("iiid", $order_id, $p_id, $qty, $price);
            $stmt_items->execute();
            $update_stock = "UPDATE products SET stock = stock - ? WHERE id = ?";
            $update_stock_stmt = $conn->prepare($update_stock);
            $update_stock_stmt->bind_param("ii", $qty, $p_id);
            $update_stock_stmt->execute();
        }
        $conn->commit();
        unset($_SESSION['cart']);
        unset($_SESSION['total_price']);
        
        $success_message = "تم تسجيل طلبك بنجاح! رقم طلبك هو: #$order_id. يمكنك التوجه للمحل لاستلامه والدفع.";
    } catch (Exception $e) {
        $conn->rollback();
        $success_message = "حدث خطأ أثناء معالجة الطلب: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إتمام الطلب</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .checkout-box { background: white; padding: 30px; border-radius: 8px; max-width: 500px; margin: auto; text-align: center; }
        .btn { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 1.1em;}
        .success { color: #27ae60; font-size: 1.2em; font-weight: bold; margin-bottom: 20px;}
    </style>
</head>
<body>

<div class="checkout-box">
    <?php if ($success_message): ?>
        <p class="success"><?php echo $success_message; ?></p>
        <a href="index.php" class="btn">العودة للرئيسية</a>
    <?php else: ?>
        <h2>إتمام عملية الشراء</h2>
        <p>المبلغ الإجمالي المطلوب: <strong><?php echo $total_price; ?> ل.س</strong></p>
        <p style="color: #7f8c8d; font-size: 0.9em;">* طريقة الدفع: نقداً عند استلام الطلب من المحل.</p>
        
        <form method="POST" action="checkout.php">
            <button type="submit" class="btn">تأكيد الطلب</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>