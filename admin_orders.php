<?php  
session_start();
include 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("غير مسموح لك بالدخول ");
    exit();
}
if (isset($_GET['action']) && isset($_GET['id'])) {
    $order_id = $_GET['id'];
    if ($_GET['action'] === 'complete'){
        $stmt= $conn->prepare("UPDATE orders SET status = 'مكتمل' WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
    } elseif ($_GET['action'] === 'delete') {
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
    }
    header("Location: admin_orders.php");
    exit();
}
$orders_result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الطلبات</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #666;
            color: white;
        }
        .btn-complete {
            background-color: #28a745;
            color: white;
            padding: 6px 12px;
            border: none;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            border: none;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-complete:hover {
            background-color: #218838;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;margin-top:20px; color:#27a056">قائمة الطلبات الواردة</h2>
    <table>
        <tr>
            <th>رقم الطلب</th>
            <th>رقم المستخدم</th>
            <th>المبلغ الإجمالي</th>
            <th>تاريخ الطلب</th>
            <th>حالة الطلب</th>
        </tr>
        <?php while ($order = $orders_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo $order['user_id']; ?></td>
                <td><?php echo $order['total_price']; ?> ل.س</td>
                <td><?php echo $order['created_at']; ?> </td>
                <td dir="ltr"><?php echo $order['created_at']; ?></td>

<?php 
    $current_status = trim($order['status']); 
    $is_completed = ($current_status === 'مكتمل');
?>
<td style="font-weight: bold; color: <?php echo $is_completed ? 'green' : '#ff9800'; ?>;">
    <?php echo $is_completed ? 'مكتمل (تم التسليم)' : 'قيد التجهيز'; ?>
</td>

<td>
    <?php if(!$is_completed): ?>
        <a href="?action=complete&id=<?php echo $order['id']; ?>" class="btn-complete">تحديد كمكتمل</a>
    <?php endif; ?>
    
    <a href="?action=delete&id=<?php echo $order['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا الطلب؟');">حذف</a>
</td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>