<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=UTF-8");
header("Access-Control-Allow-Methods: POST" );

include 'db.php';
include 'jwt_helper.php';

$data = json_decode(file_get_contents("php://input"));
if (isset($data->email) && isset($data->password)) {
    $email = $data->email;
    $password = $data->password;
    $stmt = $conn->prepare("SELECT id , password , role FROM users WHERE email = ?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user =$result->fetch_assoc()) {
        if (password_verify($password,$user['password'])) {
            $payload= [
            'user_id' => $user['id'],
            'role' => $user['role'],
            'exp' => time() + (60 * 60 *24)
            ];
            $token = generate_jwt($payload);
            echo json_encode(["message" => "تم التسجيل الدخول بنجاح" , "token" => $token]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => "كلمة السر غير صحيحة"]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["error" => "المستخدم غير موجود"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "يرجى إدخال البريد الإلكتروني وكلمة المرور"]);
}
?>