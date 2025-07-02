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

$stmt = $pdo->query("SELECT * FROM products ORDER BY RAND()");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - All Products</title>
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
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            padding: 20px 0;
        }

        .product-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .product-image-container {
            position: relative;
            width: 80%;
            padding-top: 50%; 
            overflow: hidden;
        }

        .product-image {
            position: relative;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            align-items: center;

            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-info {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .product-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #2c3e50;
            font-weight: 600;
            line-height: 1.4;
        }

        .product-description {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 15px;
            flex-grow: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5;
        }

        .product-price {
            font-size: 1.3rem;
            color: #2980b9;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .product-category {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-top: auto;
            padding-top: 12px;
            border-top: 1px solid #ecf0f1;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.75);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .modal-overlay.active {
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 1;
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 900px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            padding: 25px;
            border-bottom: 1px solid #ecf0f1;
            background-color: #f8f9fa;
        }

        .modal-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .close-modal {
            font-size: 28px;
            cursor: pointer;
            color: #7f8c8d;
            transition: color 0.2s ease;
        }

        .close-modal:hover {
            color: #34495e;
        }

        .modal-body {  
            padding: 30px;
        }

        .product-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: center;
          
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        
        }
            
        .modal-product-image-container {
            position: relative;
            width: 100%;
            padding-top: 75%;
            border-radius: 8px;
            overflow: hidden;
        }

        .modal-product-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            background-color: #f8f9fa;
        }

        .product-meta {
            margin-top: 25px;
        }

        .product-meta p {
            margin: 12px 0;
            color: #666;
            font-size: 1rem;
            line-height: 1.5;
        }

        .product-meta strong {
            color: #2c3e50;
            font-weight: 600;
        }

        .product-actions {
            margin-top: 30px;
            display: flex;
            gap: 15px;
        }

        .btn-wishlist, .btn-cart {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
        }

        .btn-wishlist {
            background-color: #f8f9fa;
            color: #e74c3c;
            border: 2px solid #e74c3c;
        }

        .btn-wishlist:hover {
            background-color: #e74c3c;
            color: white;
        }

        .btn-cart {
            background-color: #3498db;
            color: white;
            border: 2px solid #3498db;
        }

        .btn-cart:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .btn-wishlist.active {
            background-color: #e74c3c;
            color: white;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }


            .navbar-brand{
            font-family: 'poppins', sans-serif;
            fony-weight: bold;
            font-size: 40px;
        }
        .nav-link{
            padding: 0.5rem 1 rem;
        }


            .navbar-nav {
                margin: 0;
                padding: 0;
            }

            .nav-item {
                margin: 0;
                padding: 0;
            }

            .nav-link {
                padding: 0.8rem 1.5rem;
                color: #333;
                border-bottom: 1px solid #eee;
                transition: all 0.3s ease;
            }

            .nav-item:last-child .nav-link {
                border-bottom: none;
            }

            .nav-link:hover {
                background-color: #f8f9fa;
                color: #007bff;
            }

            .navbar-toggler {
                border: none;
                padding: 0.5rem;
                outline: none !important;
            }

            .navbar-toggler:focus {
                box-shadow: none;
            }

            .navbar-toggler-icon {
                width: 1.5em;
                height: 1.5em;
            }

            .product-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .product-card {
                margin-bottom: 15px;
            }

            .product-card img {
                height: 200px;
                object-fit: cover;
            }

            .modal-content {
                width: 95%;
                margin: 10px;
            }

            .product-actions {
                flex-direction: column;
                gap: 10px;
            }

            .btn-wishlist, .btn-cart {
                width: 100%;
                justify-content: center;
            }

            #notification-area {
                width: 90%;
                left: 5%;
                right: 5%;
            }
        }

        /* Notification Styles */
        #notification-area {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
        }

        .notification {
            padding: 15px 25px;
            margin-bottom: 10px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            animation: slideIn 0.3s ease-out;
        }

        .notification.success {
            background-color: #2ecc71;
        }

        .notification.error {
            background-color: #e74c3c;
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
        <h1 class="mb-4">All Products</h1>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="product-card" data-product='<?php echo json_encode($product); ?>'>
                        <div class="product-image-container">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 class="product-image" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="product-price">₹<?php echo number_format($product['price'], 2); ?></div>
                            <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <div class="modal-overlay" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalProductName"></h2>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <div class="product-details-grid">
                    <div class="modal-product-image-container">
                        <img id="modalProductImage" class="modal-product-image" src="" alt="">
                    </div>
                    <div class="product-info">
                        <p id="modalProductDescription"></p>
                        <div class="product-meta">
                            <p><strong>Price:</strong> ₹<span id="modalProductPrice"></span></p>
                            <p><strong>Category:</strong> <span id="modalProductCategory"></span></p>
                            <p><strong>Stock Quantity:</strong> <span id="modalProductStock"></span></p>
                        </div>
                        <div class="product-actions">
                            <button class="btn-wishlist" id="btnWishlist">
                                <i class="bi bi-heart"></i> Add to Wishlist
                            </button>
                            <button class="btn-cart" id="btnCart">
                                <i class="bi bi-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productCards = document.querySelectorAll('.product-card');
            const modal = document.getElementById('productModal');
            const closeModal = document.querySelector('.close-modal');
            const btnWishlist = document.getElementById('btnWishlist');
            const btnCart = document.getElementById('btnCart');
            const notificationArea = document.getElementById('notification-area');
            
            let currentProductId = null;
            let isInWishlist = false;
            const csrfToken = document.getElementById('csrf_token').value;

            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.textContent = message;
                notificationArea.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            function checkWishlistStatus(productId) {
                fetch('actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=check_wishlist&product_id=${productId}&csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    isInWishlist = data.in_wishlist;
                    updateWishlistButton();
                });
            }

            function updateWishlistButton() {
                btnWishlist.innerHTML = `<i class="bi bi-heart${isInWishlist ? '-fill' : ''}"></i> ${isInWishlist ? 'Remove from' : 'Add to'} Wishlist`;
                btnWishlist.classList.toggle('active', isInWishlist);
            }

            function openModal(productData) {
                currentProductId = productData.id;
                document.getElementById('modalProductImage').src = productData.image_url;
                document.getElementById('modalProductName').textContent = productData.name;
                document.getElementById('modalProductDescription').textContent = productData.description;
                document.getElementById('modalProductPrice').textContent = parseFloat(productData.price).toFixed(2);
                document.getElementById('modalProductCategory').textContent = productData.category;
                document.getElementById('modalProductStock').textContent = productData.stock_quantity;
                
                checkWishlistStatus(productData.id);
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            function closeModalHandler() {
                modal.classList.remove('active');
                document.body.style.overflow = '';
                currentProductId = null;
            }
            
            productCards.forEach(card => {
                card.addEventListener('click', function() {
                    const productData = JSON.parse(this.dataset.product);
                    openModal(productData);
                });
            });
            
            closeModal.addEventListener('click', closeModalHandler);
            
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModalHandler();
                }
            });
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    closeModalHandler();
                }
            });

            btnWishlist.addEventListener('click', function(e) {
                e.stopPropagation();
                if (!currentProductId) return;

                const action = isInWishlist ? 'remove_from_wishlist' : 'add_to_wishlist';
                fetch('actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=${action}&product_id=${currentProductId}&csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        isInWishlist = !isInWishlist;
                        updateWishlistButton();
                        showNotification(data.message);
                    } else {
                        showNotification(data.message, 'error');
                    }
                });
            });

            btnCart.addEventListener('click', function(e) {
                e.stopPropagation();
                if (!currentProductId) return;

                fetch('actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=add_to_cart&product_id=${currentProductId}&csrf_token=${csrfToken}`
                })
                .then(response => response.json())
                .then(data => {
                    showNotification(data.message, data.success ? 'success' : 'error');
                });
            });
        });
    </script>
</body>
</html> 