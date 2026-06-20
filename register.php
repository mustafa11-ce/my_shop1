<?php
session_start();
include 'db.php';
$error= "";
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    // var_dump($_POST); die();
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_POST['phone'];
    if ($password !== $confirm_password){
        $error = "كلمة السر غير متطابقة";
    } elseif (strlen($password)<8 || 
            !preg_match('/[A-Z]/',$password) ||
            !preg_match('/[a-z]/',$password) ||
            !preg_match('/[0-9]/',$password) ||
            !preg_match('/[\w_]/',$password) ){
        $error = " يجب أن تكون كلمة السر مكونة من أحرف صغيرة وحرف كبير ورمز ورقم وأن تكون من 8 أحرف على الأقل";
    } else {
        $stmt = $conn->prepare("SELECT id  FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
            $stmt->store_result();
        if ($stmt->num_rows > 0){
            $error = "البريد الإلكتروني مستخدم بالفعل";
        }else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt =$conn->prepare("INSERT INTO users (full_name, email , password, phone, role ,is_verified) VALUES (? ,?,?,?, 'customer',1)");
            $insert_stmt->bind_param("ssss",$full_name,$email,$hashed_password,$phone);
            if ($insert_stmt-> execute()){
                header("Location: login.php?msg=success");
                exit();
            }else{
                $error = "حدث خطأ أثناء الحفظ";
            }
            
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل حساب جديد</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form method="POST" style="max-width: 400px; margin:auto; padding:20px;">
        <h2>تسجيل حساب جديد</h2>
        <?php if ($error): ?>
            <p style="color:red; font-weight:bold"><?php echo $error; ?></p>
        <?php endif; ?>
        <label for="full_name">الاسم الكامل:</label>
        <input type="text" id="full_name" name="full_name" required style="width: 100%; margin-bottom: 10px;">
        
        <label for="email">البريد الإلكتروني:</label>
        <input type="email" id="email" name="email" required style="width: 100%; margin-bottom: 10px;">
        
        <label for="password">كلمة السر:</label>
        <input type="text" id="password" name="password" required style="width: 100%; margin-bottom: 10px;">
        
        <label for="confirm_password">تأكيد كلمة السر:</label>
        <input type="text" id="confirm_password" name="confirm_password" required style="width: 100%; margin-bottom: 10px;">
        
        <label for="phone">رقم الهاتف:</label>
        <input type="text" id="phone" name="phone" style="width: 100%; margin-bottom: 10px;">
        
        <button type="submit">تسجيل</button>
    </form>
</body> 
</html>
