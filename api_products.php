<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=UTF-8");
header("Access-Control-Allow-Methods: GET , POST , PUT , PATCH , DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'db.php';
include 'jwt_helper.php';
$token = get_bearer_token();
$user_data = verify_jwt($token);
if (!$user_data || $user_data['exp'] < time()) {
    http_response_code(401);
    echo json_encode(["error" => "غير مصرح لك بالوصول أو التوكن منتهي الصلاحية"]);
    exit();
}
$method = $_SERVER['REQUEST_METHOD'];
$input_data = json_decode(file_get_contents("php://input"),true);
switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $conn->prepare("SELECT * FROM pruducts WHERE id = ?");
            $stmt->bind_param("i",$_GET['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            echo json_encode($product ? $product : ["message" => "المنتج غير موجود"]);
        } else {
            $result = $conn->query("SELECT * FROM products");
            $product = [];
            while ($row = $result->fetch_assoc()) {
                $product[] = $row ;
            }
            echo json_encode($product);
        }
        break;
    case 'POST' :
        if ($user_data['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode((["error" => "لا تمتلك الصلاحيات"]));
            exit();
        }
            $title = $input_data['title'];
            $price = $input_data['price'];
            $description = $input_data['description'] ?? '';
            $stock = $input_data['stock'] ?? 0;
            $stmt = $conn->prepare("INSERT INTO products (title, price, description, stock) VALUES (?, ?, ?, ?)");                $stmt->bind_param("sdsi",$title, $price, $description, $stock);
            if ($stmt->execute()) {
                echo json_encode(["message" => "تم إضافة المنتج بنجاح" , "id" => $conn->insert_id]);
            } else {
                echo json_encode(["error" => "حدث خطأ أثناء الإضافة"]);
            }
        break;
    case 'PUT':
    case 'PATCH':
        if ($user_data['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "لا تمتلك الصلاحيات"]);
            exit();
        }
        if (isset($_GET['id'])){
            $id = $_GET['id'];
            $title = $input_data['title'];
            $price = $input_data['price'];
            $stmt = $conn->prepare("UPDATE products SET title = ? , price = ? WHERE id = ?");
            $stmt->bind_param("sdi", $title , $price , $id);
            if ($stmt->execute()) {
                echo json_encode(["message" => "تم تعديل المنتج بنجاح"]);
            } else {
                echo json_encode(["error" => "فشل التعديل"]);
                }
        } else {
            echo json_encode(["error" => "يرجي تحديد معرف المنتج في الرابط"]);
            }
        break;
    case 'DELETE': 
        if ($user_data['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "لا تمتلك الصلاحيات"]);
            exit();
        }
        if (isset($_GET['id'])) {
            $stmt =  $conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt->bind_param("i",$_GET['id']);
            if ($stmt->execute()) {
                echo json_encode(["message" => "تم حذف المنتج بنجاح"]);
            }
        }else {
            echo json_encode(["erro" => "يرجى تحديد معرف المنتج ليتم الحذف"]);
        }
        break;
        default: 
        http_response_code(405);
        echo json_encode(["error" => "طريقة الطلب غير صحيحة"]);
        break;
}
?>
