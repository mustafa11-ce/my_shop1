<?php
session_start();
include 'db.php';  
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("غير مسموح لك بالدخول لهذه الصفحة.");
}
$message = "";
$edit_mode = false;
$current_product = [
    'id' => '',
    'title' => '',
    'category_id' => '',
    'price' => '',
    'stock' => '',
    'description' => '',
    'image_url' => ''
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$product_id = $_POST['product_id'] ?? '';
$title = $_POST['title'] ?? '';
$category_id = $_POST['category_id'] ?? '';
$price = $_POST['price'] ?? '';
$stock = $_POST['stock'] ?? '';
$description = $_POST['description'] ?? '';
$image_url = $_POST['old_image'] ?? '';
if (!empty($_FILES['images']['name'][0])) {
    $upload_dir= "uploadssalin/";
    $paths_arry = [];
    foreach ($_FILES['images']['name'] as $i => $name) {
        $target_file = $upload_dir . time() . "_" . basename($name);
        move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_file);
        $paths_arry[] = $target_file;{
            $image_url = implode(",", $paths_arry);
        }
    }
    if (!empty($uploaded_images)) {
        $image_url = implode(",", $uploaded_images);
    }
}
if (isset($_POST['btn_search'])) {
    $search_term = trim($_POST['search_term']);
    if (!empty($search_term)){
        $stmt = $conn-> prepare("SELECT * FROM products WHERE id = ? or title LIKE ? LIMIT 1");
        $like_term = "%".$search_term."%";
        $stmt->bind_param("ss", $search_term, $like_term);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $current_product = $result->fetch_assoc();
            $edit_mode = true;
            $message = "تم العثور على المنتج، يمكنك تعديله أو حذفه.";
        } else {
            $message = "لم يتم العثور على المنتج.";
        }
    }
}elseif(isset($_POST['btn_add'])){
    if (!empty($title) && !empty($category_id) && !empty($price) && !empty($stock)) {
        $stmt = $conn->prepare("INSERT INTO products (category_id, title, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdis", $category_id, $title, $description, $price, $stock, $image_url);
        if ($stmt->execute()) {
            $message = "تمت إضافة المنتج بنجاح!";
        } else {
            $message = "خطأ في إضافة المنتج.";
        }
    } else {
        $message = "يرجى ملء جميع الحقول المطلوبة.";
    }
}
elseif(isset($_POST['btn_edit'])){
    if (!empty($product_id) && !empty($title) && !empty($category_id) && !empty($price) && !empty($stock)) {
        $stmt = $conn->prepare("UPDATE products SET category_id = ?, title = ?, description = ?, price = ?, stock = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("issdisi", $category_id, $title, $description, $price, $stock, $image_url, $product_id);
        if ($stmt->execute()) {
            $message = "تم تعديل المنتج بنجاح!";
        } else {
            $message = "خطأ في تعديل المنتج.";
        }
    } else {
        $message = "يرجى ملء جميع الحقول المطلوبة.";    
    }
}
elseif(isset($_POST['btn_delete'])){
    if (!empty($product_id)) {
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        if ($stmt->execute()) {
            $message = "تم حذف المنتج بنجاح!";
            $edit_mode = false;
            $current_product = [
                'id' => '',
                'title' => '',
                'category_id' => '',
                'price' => '',
                'stock' => '',
                'description' => '',
                'image_url' => ''
            ];
        } else {
            $message = "خطأ في حذف المنتج.";
        }
    } else {
        $message = "يرجى إدخال معرف المنتج لحذفه.";
    }
}}
if (isset($_GET['action']) && $_GET['action'] === 'load' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $current_product = $result->fetch_assoc();
        $edit_mode = true;
    } else {
        $message = "لم يتم العثور على المنتج.";
    }
}
$product_result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl"> 
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافات وتعديل المنتجات</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }
        .containar {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .message{
            background-color: #e2f3e5;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold ;
            border: 1px solid #c6e0cb;
        }
        .search-box{
            background-color: #eef2f5;
            padding: 15px;
            border-radius: 5px;
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            gap: 10px;
            border: 1px solid #ddd;
        }
        .search-box input{
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-grid{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .form-grid input, .form-grid select, .form-grid textarea{
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-grid textarea{
            grid-column: span 2;
            height: 80px;
        }
        button{
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-search{
            background-color: #007bff;
        
        }
        .btn-search:hover{
            background-color: #0056b3;
        }
        .btn_add {
            background-color: #28a745;
            width: 100%;
        }
        .btn_add:hover {
            background-color: #218838;
        }
        .btn-edit {
            background-color: #fd7e14;
        }
        .btn-edit:hover {
            background-color: #e36a0b;
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .btn-clear{
            background-color: #6c757d;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 4px;
            color: #fff;
            display: inline-block;
        }
        .action-buttons{
            display: flex;
            gap: 10px;
            margin-top:15px ;
            grid-column: span 2;
        }
        table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }
        table th, table td{
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th, td{
            padding: 12px;
            background-color: #f9f9f9;
            text-align: center;
        }
        th {
            background-color: #343a40;
            color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }
        </style>
</head>
<body> 
    <div class="container">
        <h2 style="text-align: center;color:#333">إدارة بضائع المحل</h2>
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST" class="search-box">
            <input type="text" name="search_term" placeholder="ابحث عن منتج بالمعرف أو الاسم..." value="<?php echo isset($_POST['search_term']) ? htmlspecialchars($_POST['search_term']) : ''; ?>">
            <button type="submit" name="btn_search" class="btn-search">بحث</button>
        </form>
        <form method="POST" class="form-grid" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $current_product['id']; ?>">
            <div class="form-grid">
                <input type="text" name = "title" placeholder="اسم المنتج" value="<?php echo $current_product['title']; ?>" required>
                <select name="category_id" required>
                <option value ="1" <?php echo $current_product['category_id'] == 1 ? 'selected' : ''; ?>>إلكترونيات</option>
                <option value ="2" <?php echo $current_product['category_id'] == 2 ? 'selected' : ''; ?>>أدوات منزلية</option>
                </select>
                <input type="number" step="0.01" name="price" placeholder="السعر" value="<?php echo $current_product['price']; ?>" required>
                <input type="number" name="stock" placeholder="الكمية المتوفرة" value="<?php echo $current_product['stock']; ?>" required>
                <textarea name="description" placeholder="وصف المنتج"><?php echo $current_product['description']; ?></textarea>
                <input type="file" name="images[]" multiple accept="image/*" placeholder="ارفق الصورة" >
                <input type="hidden" name="old_image" value="<?php echo $current_product['image_url'] ?? ''; ?>">
                <div class="action-buttons">
                    <button type="submit" name= "btn_add" class="btn_add"> إضافة منتج</button>
                    <?php if ($edit_mode): ?>
                        <button type="submit" name="btn_edit" class="btn-edit">تعديل المنتج</button>
                        <button type="submit" name="btn_delete" class="btn-delete" 
                        onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا المنتج؟')">حذف المنتج</button>
                        <a href="admin_add_product.php" class="btn-clear">إلغاء</a>
                    <?php endif; ?>

            </div>
    </div>
        </form>
        <hr style ="margin: 30px 0;">
        <h3 style="text-align: center;color:#333">المنتجات المتوفرة حالياً</h3>
        <table>
        <tr>
            <th>رقم (ID)</th>
            <th>اسم المنتج</th>
            <th>السعر</th>
            <th>الكمية المتوفرة</th>
            <th>الإجراءات</th>
        </tr>
        <?php while ($row = $product_result->fetch_assoc()): ?>
            <tr> 
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['price']; ?> ل.س</td>
                <td style="font-weight:bold;color:<?php echo $row['stock'] > 0 ? 'green' : 'red'; ?>;">
                    <?php echo $row['stock']; ?>
                </td>
                <td><a href="?action=load&id=<?php echo $row['id']; ?>" style="color:#007bff;text-decoration:none;font-weight:bold;">استدعاء للوحة التحكم</a></td>
            </tr>
        <?php endwhile; ?>
        
    </table>
    </div>
</body>
</html>