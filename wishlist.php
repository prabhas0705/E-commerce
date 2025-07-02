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

$stmt = $pdo->prepare("SELECT w.id as wishlist_id, p.*FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ? ORDER BY w.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
         .navbar-brand{
            font-family: 'poppins', sans-serif;
            fony-weight: bold;
            font-size: 40px;
        }
        .nav-link{
            padding: 0.5rem 1 rem;
        }

        .wishlist-item {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .wishlist-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }

        .remove-btn {
            color: #dc3545;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .remove-btn:hover {
            color: #c82333;
        }

        .add-to-cart-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-to-cart-btn:hover {
            background-color: #218838;
        }

        .empty-wishlist {
            text-align: center;
            padding: 50px 0;
        }

        .empty-wishlist i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 20px;
        }

        #notification-area {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
        }

        .notification {
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 4px;
            color: white;
            animation: slideIn 0.3s ease-out;
        }

        .notification.success {
            background-color: #28a745;
        }

        .notification.error {
            background-color: #dc3545;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
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
                        <a class="nav-link" href="cart.php">My Cart</a>
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
    <div id="notification-area"></div>
    <div class="container mt-4">
        <h1 class="mb-4">My Wishlist</h1>
        <?php if (empty($wishlistItems)): ?>
            <div class="empty-wishlist">
                <i class="bi bi-heart"></i>
                <h3>Your wishlist is empty</h3>
                <p>Add items to your wishlist while shopping!</p>
                <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($wishlistItems as $item): ?>
                    <div class="col-md-6 mb-4">
                        <div class="wishlist-item" data-wishlist-id="<?php echo $item['wishlist_id']; ?>" data-product-id="<?php echo $item['id']; ?>">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="product-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                                    <p class="text-muted mb-2">₹<?php echo number_format($item['price'], 2); ?></p>
                                    <p class="mb-0"><small class="text-muted">Stock: <?php echo $item['stock_quantity']; ?></small></p>
                                </div>
                                <div class="col-md-3 text-end">
                                    <button class="add-to-cart-btn mb-2" onclick="addToCart(<?php echo $item['id']; ?>)">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                    <br>
                                    <i class="bi bi-trash remove-btn" onclick="removeFromWishlist(<?php echo $item['wishlist_id']; ?>)"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationArea = document.getElementById('notification-area');
            const csrfToken = document.getElementById('csrf_token').value;



            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.textContent = message;
                notificationArea.appendChild(notification);
                setTimeout(() => {
                    notification.remove();
                }, 1000);
            }
            window.addToCart = function(productId) {
                fetch('actions.php', {
                    method: 'POST',
                    headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body:`action=add_to_cart&product_id=${productId}&csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                 showNotification(data.message, data.success ? 'success' : 'error');
                });
            };
            window.removeFromWishlist = function(wishlistId) {
                if (!confirm('Are you sure you want to remove this item from your wishlist?')) {
                    return;
                }
                fetch('actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=remove_from_wishlist&wishlist_id=${wishlistId}&csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = document.querySelector(`[data-wishlist-id="${wishlistId}"]`);
                        if (item) {
                            item.closest('.col-md-6').remove();
                        }
                        showNotification(data.message);
                        if (document.querySelectorAll('.wishlist-item').length === 0) {
                            location.reload();
                        }
                    } else {
                        showNotification(data.message, 'error');
                    }
                });
            };
        });
    </script>
</body>
</html> 