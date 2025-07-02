
<!DOCTYPE html>
<html>
<head>
    <title>Thank You</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
            
        body{
                background-color: #f2f2f2;
                font-family:Arial,sans-serif;
                min-height :100vh;
                display:flex;
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
        
            
        h1{
            color: black;
            font-size: 2.5em;
            text-align: center;
            margin-top: 50px;

        }

        p{
            color: #333;
            font-size: 1.2em;
            text-align: center;
            margin-top: 20px;
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
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class= "thank-you-container">
        <?php
        // Start the session
        session_start();
        if (!isset($_SESSION['order_success']) || !$_SESSION['order_success']) {
            header('Location: index.php');
            exit();
        }
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            $customerName = htmlspecialchars($user['name']);                    
        } else {      
            require_once 'config/database.php';
            $stmt = $pdo->prepare("SELECT name FROM users WHERE id=?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            $customerName = htmlspecialchars($user['name']);
            
            

        }
        $orderId = isset($_SESSION['order_id']) ? htmlspecialchars($_SESSION['order_id']) : '';
        unset($_SESSION['order_success']);
        unset($_SESSION['order_id']);                                    
        ?>                 
        <h1>Thank You, <?php echo $customerName; ?>!</h1>
        <div class="order-details">
            <p>Your order has been received and will be delivered soon.</p>
            <?php if ($orderId): ?>
                <p>Order ID: #<?php echo $orderId; ?></p>
            <?php endif; ?>
            <p>A confirmation email has been sent with your order details.</p>
        </div>
        
    </div>
    <script src = "https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>
    </body>
    </html>

        