
<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.price, p.image_url 
    FROM cart c
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate   total amount
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">    
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Shopping Cart</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            background-color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color:rgb(23, 24, 24) !important;
        }

        .nav-link {
            font-weight: 500;
            color: #333 !important;
            padding: 0.5rem 1rem !important;
            margin: 0 0.2rem;
        }

        .nav-link.active {
            color:rgb(15, 16, 15) !important;
        }
        
        .cart-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 2rem;
            margin: 20px auto;
            max-width: 1000px;
        }
        
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 1.5rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .product-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex: 2;
        }
        
        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .product-info h5 {
            margin-bottom: 0.5rem;
            color: #333;
        }

        .product-info p {
            font-size: 1.1rem;
            color:rgb(21, 23, 22);
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex: 1;
            justify-content: center;
        }
        
        .quantity-btn {
            background-color:rgb(15, 16, 15);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .quantity-btn:hover {
            background-color:rgb(25, 27, 25);
        }
        
        .quantity-input {
            width: 60px;
            text-align: center;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 8px;
            font-size: 1rem;
        }
        
        .item-total {
            font-size: 1.1rem;
            font-weight: 500;
            color:rgb(30, 31, 30);
            flex: 1;
            text-align: right;
        }
        
        .remove-btn {
            color: #dc3545;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0.5rem;
            transition: color 0.2s;
        }

        .remove-btn:hover {
            color: #c82333;
        }
        
        .cart-summary {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 2rem;
            border: 1px solid #e9ecef;
        }

        .cart-summary .d-flex {
            padding: 0.5rem 0;
            font-size: 1.1rem;
        }
        
        .checkout-btn {
            width: 100%;
            padding: 1rem;
            margin-top: 1rem;
            font-size: 1.1rem;
            background-color:rgb(27, 29, 27);
            border: none;
        }

        .checkout-btn:hover {
            background-color:rgb(26, 28, 26);
        }
        
        .empty-cart {
            text-align: center;
            padding: 4rem 0;
        }
        
        .empty-cart i {
            font-size: 5rem;
            color:rgb(29, 31, 29);
            margin-bottom: 1.5rem;
        }

        .empty-cart h3 {
            color: #333;
            margin-bottom: 1rem;
        }

        .empty-cart p {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }

        .empty-cart .btn {
            background-color:rgb(15, 16, 15);
            border: none;
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
        }

        .empty-cart .btn:hover {
            background-color:rgb(27, 28, 27);
        }

        footer {
            margin-top: auto;
            background-color:rgb(24, 25, 24) !important;
            color: white !important;
            padding: 1rem 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Sieva</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shop.php">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cart.php">My Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="wishlist.php">Wishlist</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="cart-container">
            <h1 class="mb-4">Shopping Cart</h1>
            
            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <i class="bi bi-cart-x"></i>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added any items to your cart yet.</p>
                    <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item" data-cart-id="<?php echo $item['id']; ?>">
                        <div class="product-info">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="product-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div>
                                <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                                <p class="text-muted mb-0">₹<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                        </div>
                        <div class="quantity-controls">
                            <button class="quantity-btn decrease" onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)">-</button>
                            <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1" onchange="updateQuantity(<?php echo $item['id']; ?>, this.value)">
                            <button class="quantity-btn increase" onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)">+</button>
                        </div>
                        <div class="item-total">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                        </div>
                        <i class="bi bi-trash remove-btn" onclick="removeItem(<?php echo $item['id']; ?>)"></i>
                    </div>
                <?php endforeach; ?>
  

             
                <div class="cart-summary">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>₹<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping:</span>
                        <span>Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong>₹<?php echo number_format($total, 2); ?></strong>
                    </div>
                    <a href="checkout.php" class="btn btn-primary checkout-btn">Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateQuantity(cartId, newQuantity) {
            if (newQuantity < 1) return;
            
            $.ajax({
                url: 'actions.php',
                type: 'POST',
                data: {
                    action: 'update_cart',
                    cart_id: cartId,
                    quantity: newQuantity,
                    csrf_token: $('#csrf_token').val()
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
        function removeItem(cartId) {
            if (!confirm('Are you sure you want to remove this item?')) return;
            
            $.ajax({
                url: 'actions.php',
                type: 'POST',
                data: {
                    action: 'remove_from_cart',
                    cart_id: cartId,
                    csrf_token: $('#csrf_token').val()
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    </script>
</body>
</html>