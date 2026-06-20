<?php
session_start();
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password, role, image,is_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password,$user['password'])) {
            if ($user['is_verified']==1){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_image'] = $user['image'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
        } else {
            $error = "عذراً يجب تأكيد حسابك أولاً";
        }
        }else{
            $error = 'كلمة المرور غير صحيحة';
        }

    } else {
        $error = "المستخدم غير موجود.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> تسجيل الدخول</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form method="POST" style="max-width: 400px; margin: auto; padding: 20px;">
    <h2>تسجيل الدخول</h2>
    <?php if (isset($error)) { echo "<p style='color:red; text-align:center;font-weight:bold;'>$error</p>"; } ?>
    <input type="email" name="email" placeholder="البريد الإلكتروني" required style="width:100%; margin-bottom:10px;">
    <input type="password" name="password" placeholder="كلمة المرور" required style="width:100%; margin-bottom:10px;">
    <button type="submit">دخول</button>
</form>
</body>
</html>