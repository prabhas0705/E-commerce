<?php
session_start();
require_once 'config/database.php';

// if  not logged in redirect to page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


$categories = ['Electronics', 'Clothing', 'Home & Kitchen', 'Books', 'Sports & Outdoors', 'Toys & Games', 'Beauty & Personal Care'];

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
 
$query = "SELECT * FROM products WHERE 1";
$params = [];

if ($search) {
    $query .= " AND name LIKE ?";
    $params[] = "%$search%";
}

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sieva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">    
    <link rel="stylesheet" href="./css/styles.css">
    <style>

        body{
            padding-top:56px;
        }
        @media(max-width:768px){
            .container{
                padding-left:15px;
                padding-right:15px;
            }
        }
        .navbar-brand{
            font-family: 'poppins', sans-serif;
            fony-weight: bold;
            font-size: 40px;
        }
        .nav-link{
            padding: 0.5rem 1 rem;
        }
        .product-card{
            margin-bottom:15px;

        }
        .product-card img{
            height:200px;
            object-fit:cover;
        }
        .modal-content{
            width:95%;
            margin:10px;
        }
        .product-actions{
            flex-direction:column;
            gap:10px;
        }
        .btn-wishlist, .btncart{
            width:100%;
            justify-content:centre;
        }
        #notification-area {
            width:90%;
            left: 5%;
            right:5%;
        }

        .form-control, .btn {
            border-radius: 0;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 1;
        }

        .modal-content {
            background: white;
            width: 80%;
            max-width: 800px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }       
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            color: #333;
        }

        .close-modal {
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .close-modal:hover {
            color: #333;
        }

        .modal-body {
            padding: 20px;
        }                    
        .product-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .product-image img {
            width: 100%;
            height: auto;
            border-radius: 4px;
            object-fit: cover;
        }

        .product-info h3 {
            margin-top: 0;
            color: #333;
        }

        .product-meta {
            margin-top: 20px;
        }

        .product-meta p {
            margin: 8px 0;
            color: #666;
        }

        .product-meta strong {
            color: #333;
        }

        .product-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .btn-wishlist, .btn-cart {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-wishlist {
            background-color: #f8f9fa;
            color: #dc3545;
        }

        .btn-wishlist:hover {
            background-color: #dc3545;
            color: white;
        }

        .btn-cart {
            background-color: #007bff;
            color: white;
        }

        .btn-cart:hover {
            background-color: #0056b3;
        }

        .btn-wishlist.active {
            background-color: #dc3545;
            color: white;
        }

       
        .list-group-item{
            cursor: pointer;
            text-decoration: none;
        }

        .list-group-item:hover {
            background-color :white;
            color : black;
            text-decoration: none;
        }

       

        
        .nav-item{
            color : black;
        }
        ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  color:black;
}
        .nav-item li { 
            color: black;
        
        }

        .list-inline-item {
            color:black;

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

        .modal-footer {
         border-top: 1px solid #eee;
         align-items: center;
                                                          
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
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">Sieva </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!--navigation links-->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="admin/products.php">Admin
                        <i class = "bi bi-person-circle"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="shop.php">Shop</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="cart.php">My Cart</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="wishlist.php">Wishlist</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>

            </ul>
        </div>
    </div>
</nav>

    <div id="notification-area" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
    <div class="container mt-5">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#Home" type="button" role="tab" aria-controls="home" aria-selected="true">Home</button>
            </li>

        </ul>
        <div class="tab-content" id="myTabContent">

            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <h2>Shop by Categories</h2>
                <form method="GET" action="index.php" class="mb-3">
                    
                <div class="row">

                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search products" value="<?php echo htmlspecialchars($search); ?>">
                        </div>

                        <div class="col-md-4">
                            <!--category drop down-->

                            <select name="category" class="form-control">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat; ?>" <?php if ($cat === $category) echo 'selected'; ?>><?php echo $cat; ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>

                    </div>

                </form>

                <div class="row">
                    <div class="col-md-3">
                        <h4>Categories</h4>

                        <ul class="list-group">
                            <?php foreach ($categories as $cat): ?>

                                <li class="list-group-item">
                                    <a href="?category=<?php echo urlencode($cat); ?>"><?php echo $cat; ?></a>
                                </li>

                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <?php foreach ($products as $product): ?>
                                <div class="col-md-4 mb-4">
                                   
                                <div class="card product-card" style="cursor: pointer;" 
                                         data-product='<?php echo json_encode($product); ?>'>
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                            <p class="card-text">₹<?php echo number_format($product['price'], 2); ?></p>
                                        </div>
                                    </div>
                                
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">Profile content...</div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <div class="modal-overlay" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Product Details</h2>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <div class="product-details-grid">
                    <div class="product-image">
                        <img id="modalProductImage" src="" alt="">
                    </div>                   
                    <div class="product-info">
                        <h3 id="modalProductName"></h3>
                        <p id="modalProductDescription"></p>
                        
                        <div class="product-meta">
                            <p><strong>Price:</strong> ₹<span id="modalProductPrice"></span></p>
                            <p><strong>Category:</strong> <span id="modalProductCategory"></span></p>
                            <p><strong>Stock Quantity:</strong> <span id="modalProductStock"></span></p>                  
                        </div>
                        
                        <div class="product-actions">
                            
                        <button class="btn-wishlist" id="btnWishlist">
                                
                        <i class="bi bi-heart"></i> Add to Wishlist</button>
                        
                        <button class="btn-cart" id="btnCart">
                               <i class="bi bi-cart"></i> Add to Cart</button>
                        
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer>
<div class="container">
<div class="row">
<div class="col-md-4 footer-column">
<ul class="nav flex-column">
<li class="nav-item">
<span class="footer-title">Product</span>
</li>
<li class="nav-item">
<a class="nav-link" href="#">Product Overview</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#">Search products</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#">Plans & Prices</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#">Frequently asked questions</a>
</li>
</ul>
</div>
<div class="col-md-4 footer-column">
<ul class="nav flex-column">
<li class="nav-item">
<span class="footer-title">Company</span>
</li>
<li class="nav-item">
<a class="nav-link" href="#">About us</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#">Job postings</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#">News and articles</a>
</li>
</ul>
</div>
<div class="col-md-4 footer-column">
<ul class="nav flex-column">
<li class="nav-item">
<span class="footer-title">Contact & Support</span>
</li>
<li class="nav-item">
<span class="nav-link"><i class="fas fa-phone"></i>+91 7386566624</span>
</li>
<li class="nav-item">
<a class="nav-link" href="#"><i class="fas fa-comments"></i>Live chat</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#"><i class="fas fa-envelope"></i>Contact us</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#"><i class="fas fa-star"></i>Give feedback</a>
</li>
</ul>
</div>
</div>

<div class="text-center"><i class="fas fa-ellipsis-h"></i></div>

<div class="row text-center">
<div class="col-md-4 box">
<span class="copyright quick-links">Copyright &copy; Sieva <script>document.write(new Date().getFullYear())</script>
</span>
</div>
<div class="col-md-4 box">
<ul class="list-inline social-buttons">
<li class="list-inline-item">
<a href="#">
<i class="fab fa-twitter"></i>
</a>
</li>
<li class="list-inline-item">
<a href="#">
<i class="fab fa-facebook-f"></i>
</a>
</li>
<li class="list-inline-item">
<a href="#">
<i class="fab fa-linkedin-in"></i>
</a>
</li>
</ul>
</div>
<div class="col-md-4 box">
<ul class="list-inline quick-links">
<li class="list-inline-item">
<a href="#">Privacy Policy</a>
</li>
<li class="list-inline-item">
<a href="#">Terms of Use</a>
</li>
</ul>
</div>
</div>
</div>
</footer>
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
                }, 1000);
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

                const productImage =  document.getElementById('modalProductImage');
                const productName = document.getElementById('modalProductName');
                const productDescription = document.getElementById('modalProductDescription');
                const productPrice =       document.getElementById('modalProductPrice');
                const productCategory = document.getElementById('modalProductCategory');
                const productStock = document.getElementById('modalProductStock');

                productImage.src = productData.image_url;
                productName.textContent = productData.Name;
                productDescription.textContent = productData.Description;
                productPrice.textContent  = parseFloat(productData.price ).toFixed(2);
                productCategory.textContent = productData.Category;
                productStock.textContent = productData.stock_quantity;
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