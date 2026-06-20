<?php
define('SECRET_KEY','M_S_secret_key_Aa1122');
function generate_jwt($payload) {
    $header = json_encode(['typ' => 'JWT','alg' => 'HS256']);
    $base64UrlHeader = str_replace(['+','/','='] , ['-','_',''],base64_encode($header));
    $base64UrlPayload = str_replace(['+','/','='] , ['-','_',''],base64_encode(json_encode($payload)));
    $signature = hash_hmac('sha256',$base64UrlHeader . "." . $base64UrlPayload,SECRET_KEY,true);
    $base64UrlSignature = str_replace(['+','/','='] , ['-','_',''],base64_encode($signature));
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}
function verify_jwt($token) {
    $tokenParts = explode('.',$token);
    if (count($tokenParts) != 3) 
        return false;
    $header = $tokenParts[0];
    $payload = $tokenParts[1];
    $signature_provided = $tokenParts[2];
    $signature = hash_hmac('sha256', $header . "." . $payload ,SECRET_KEY,true);
    $base64UrlSignature = str_replace(['+','/','='] , ['-','_',''],base64_encode($signature));
    if ($base64UrlSignature === $signature_provided){
        return json_decode(base64_decode($payload),true);
    }
    return false;
}
function get_bearer_token() {
    $header = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER['Authorization']);
    }elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
    }elseif(function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords',array_keys($requestHeaders)),array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/',$headers,$matches)) {
            return $matches[1];
        }
    }
    return null;
}
?>