<?php
session_start();
if (isset($_POST['product_id'])){
    $product_id = (int)$_POST['product_id'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    } 
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
    header('Location: cart.php');
    exit();
}else {
    header('Location: index.php');
    exit();
    }
?>