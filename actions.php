<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

// Checking if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit();
}

if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$action = $_POST['action'];
$user_id = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'add_to_wishlist':
            if (!isset($_POST['product_id'])) {
                echo json_encode(['success' => false, 'message' => 'Product ID is required']);
                exit();
            }
            $product_id = $_POST['product_id'];
            $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
            try {
                $stmt->execute([$user_id, $product_id]);
                echo json_encode([
                    'success' => true, 
                'message' => 'Added to wishlist']);
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { 
                    echo json_encode([
                    'success' => false, 
                    'message' => 'Already in wishlist']);
                } else {
                    throw $e;
                }
            }
            break;

        case 'remove_from_wishlist':
            if (!isset($_POST['wishlist_id'])) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Wishlist ID is required']);
                exit();
            }
            $wishlist_id = $_POST['wishlist_id'];
            $stmt = $pdo->prepare("DELETE FROM wishlist WHERE id = ? AND user_id = ?");
            $stmt->execute([$wishlist_id, $user_id]);
            echo json_encode([
                'success' => true, 
                'message' => 'Removed from wishlist']);
            break;

        case 'check_wishlist':
            if (!isset($_POST['product_id'])) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Product ID is required']);
                exit();
            }
            $product_id = $_POST['product_id'];
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            $in_wishlist = $stmt->fetchColumn() > 0;
            echo json_encode([
                'success' => true, 
                'in_wishlist' => $in_wishlist]);
            break;

        case 'add_to_cart':
            if (!isset($_POST['product_id'])) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Product ID is required']);
                exit();
            }
            $product_id = $_POST['product_id'];
           
            $stmt = $pdo->prepare("SELECT c.quantity as cart_quantity, p.stock_quantity FROM cart c JOIN products p ON c.product_id = p.id 
                                 WHERE c.user_id = ? AND c.product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            $existing = $stmt->fetch();

            if ($existing) {
                // Check if new quantity would exceed stock
                $new_quantity = $existing['cart_quantity'] + 1;
                if ($new_quantity > $existing['stock_quantity']) {
                    echo json_encode([
                        'success' => false, 
                    'message' => 'Cannot add more than available stock']);
                    exit();
                }
                
                $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$new_quantity, $user_id, $product_id]);
            } else {
                
                $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
                $stmt->execute([$user_id, $product_id]);
            }
            echo json_encode(['success' => true, 'message' => 'Added to cart']);
            break;

        case 'update_cart':
            if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                exit();
            }
            $cart_id = $_POST['cart_id'];
            $quantity = (int)$_POST['quantity'];
            if ($quantity < 1) {
                echo json_encode(['success' => false, 'message' => 'Quantity cannot be less than 1']);
                exit();
            }
            $stmt = $pdo->prepare("SELECT p.stock_quantity FROM cart c  JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
            $stmt->execute([$cart_id, $user_id]);
            $product = $stmt->fetch();

            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Item not found']);
                exit();
            }

            if ($quantity > $product['stock_quantity']) {
                echo json_encode(['success' => false, 'message' => 'Cannot exceed available stock']);
                exit();
            }
            
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$quantity, $cart_id, $user_id]);
            echo json_encode(['success' => true, 'message' => 'Cart updated']);
            break;

        case 'remove_from_cart':
            if (!isset($_POST['cart_id'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                exit();
            }
            $cart_id = $_POST['cart_id'];                   
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->execute([$cart_id, $user_id]);
            echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
            break;                        
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}                         
?>                                                                              