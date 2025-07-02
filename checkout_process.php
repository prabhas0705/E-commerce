<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}      
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid request');
}
function encryptCardNumber($cardNumber) {
   
    return password_hash($cardNumber, PASSWORD_DEFAULT);
}
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT c.*, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_amount = 0;
    foreach ($cartItems as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, shipping_city, shipping_state, shipping_zip) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $total_amount,
        $_POST['address'],
        $_POST['city'],
        $_POST['state'],
        $_POST['zip']
    ]);
    $order_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        $stmt->execute([
            $order_id, $item['product_id'],
            $item['quantity'], $item['price']
        ]);

        $updateStock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        $updateStock->execute([$item['quantity'], $item['product_id']]);
    }

    $stmt = $pdo->prepare("
        INSERT INTO payment_info (order_id, cardholder_name, card_number, expiry_month, expiry_year) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$order_id, $_POST['cardholder'], encryptCardNumber($_POST['cardnumber']), $_POST['expmonth'], $_POST['expyear']]);

    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $pdo->commit();

    $_SESSION['order_success'] = true;
    $_SESSION['order_id'] = $order_id;
    header('Location: thanks.php');
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['checkout_error'] = "An error has been occurred while processing your order. we will help you resolev this issue
    please try again.";
    header('Location: checkout.php');
    exit();

}                                     