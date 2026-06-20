<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$message = "";
$query ="SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $image_name = $user['image'];
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/";
        $image_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $_SESSION['user_image'] = $image_name;
        } else {
            $message = "خطأ في رفع الصورة.";
        }
    }

$update_query = "UPDATE users SET full_name = ?, email = ?, image = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("sssi", $full_name, $email, $image_name, $user_id);
if ($update_stmt->execute()) {
    $message = "<div style='color:#4caf50; text-align:center;font-weight:bold;'>تم تحديث الملف الشخصي بنجاح.</div>";
    $user['full_name'] = $full_name;
    $user['email'] = $email;
    $user['image'] = $image_name;
    } else {
        $message = "<div style='color:#f44336; text-align:center;font-weight:bold;'>خطأ في تحديث الملف الشخصي.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>الملف الشخصي</title>
        <style>
                body {
                    font-family: 'segoe ui', Tahoma, Geneva, Verdana, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 20px;
                }
                .navbar{
                    background-color: #333;
                    padding: 15px 20px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .container{
                    max-width: 500px;
                    margin: 50px auto;
                    border-radius: 8px;
                    background-color: white;
                    padding: 30px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .profile-img{
                    width: 120px;
                    height: 120px;
                    border-radius: 50%;
                    object-fit: cover;
                    border: 3px solid #4caf50;
                    display: block;
                    margin: 0 auto 20px;
                }
                .form-group{
                    margin-bottom: 20px;
                }
                label{
                    display: block;
                    margin-bottom: 5px;
                    font-weight: bold;
                }
                input [type="text"],input[type="file"]{
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-bottom: 4px;
                    box-sizing: border-radius;
                }
                .btn-save{
                    width: 100%;
                    padding: 12px;
                    background-color: #4caf50;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 16px;
                }
                .btn-save:hover{
                    background-color: #45a049;
                }
                .back-link{
                    display: block;
                    text-align: center;
                    margin-top: 15px;
                    color: #666;
                    text-decoration: none;
                }
                </style>
    </head>
    <body>
        <div class="navbar">
            <a href="index.php" style="color:white ;text-decoration:none;font-weight:bold;">الصفحة الرئيسية</a>
            <span style="color:white;">إعدادات الحساب</span>
        </div>
        <div class="container">
            <h2 style="text-align: center;">تعديل الملف الشخصي</h2>
            <?php echo $message;?>
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <img src="uploads/<?php echo!empty($user['image']) ? $user['image']: 'default-avatar.png';?>"
                class="profile-img">
                <div class="form-groub">
                    <label >اسم المتسخدم</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['full_name'] ?? '');?>" required>
                </div>
                <div class="form-groub">
                    <label >البريد الإلكتروني</label>
                    <input type="text" name="email" value="<?php echo htmlspecialchars($user['email'] ?? '');?>" required>
                </div>
                <div class="form-group">
                    <label >تغيير الصورة الشخصية</label>
                    <input type="file" name="profile_image" accept="image/*">
                </div>
                <button type="submit" class="btn-save">حفظ التغيرات</button>
            </form>
            <a href="index.php" class="back-link">العودة إلى المتجر</a>
        </div>        
    </body>
</html>