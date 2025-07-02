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
    SELECT c.*, p.name, p.price, p.stock_quantity 
    FROM cart c
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}


if (empty($cartItems)) {
    header('Location: cart.php');
    exit();
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Checkout Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
</head>
<style>
    body {
        background-color: #ffffff;
        font-family: Arial, sans-serif;
    }
    
    header {
        background-color: white;
        color: #ffffff;
        padding: 20px;
    }
    
    nav ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    
    nav li {
        display: inline-block;
        margin-right: 20px;
    }
    
    nav a {
        color:rgb(234, 218, 218);
        text-decoration: none;
    }
    
    nav a:hover {
        text-decoration: underline;
    }
    
    section {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }
    
    h1 {
        color: black;
        font-size: 32px;
        margin-bottom: 20px;
    }
    
    h2 {
        color: black;
        font-size: 24px;
        margin-bottom: 10px;
    }
    
    label {
        display: block;
        margin-bottom: 5px;
        color:rgb(16, 15, 15);
    }
    
    input[type="text"],
    input[type="email"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #cccccc;
        border-radius: 5px;
        margin-bottom: 10px;
        font-size: 16px;
    }
    
    input[type="submit"] {
        background-color: black;
        color: #ffffff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }
    
    input[type="submit"]:hover {
        background-color:rgb(11, 11, 11);
    }
    
    .order-summary {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    
    .order-summary p {
        margin: 5px 0;
        color: #333;
    }
    
    footer {
        background-color: green;
        color: #ffffff;
        padding: 20px;
        text-align: center;
    }
    
</style>

<body>
    <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Shopping Portal</a>
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
    </header>

    <section>
        <h1>Checkout</h1>
        <?php if(isset($_SESSION['checkout_error'])):?>
            <div class="alert alert-danger">
                <?php
                echo htmlspecialchars($_SESSION['checkout_error']);
                unset($_SESSION['checkout_error']);?> </div>
                <?php endif;?>



        <form action="checkout_process.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <h2>Order Summary</h2>
            <div class="order-summary">
                <p><strong>Total Items:</strong> <?php echo count($cartItems); ?></p>
                <p><strong>Total Amount:</strong>₹<?php echo number_format($total, 2); ?></p>
            </div>

            <h2>Billing Information</h2>
            <label for="name">Name:</label>
            <input type="text" 
                   id="name"
                   name="name" required>

            <label for="email">Email:</label>
            <input type="email" 
                   id="email" 
                   name="email" required>

            <label for="address">Address:</label>
            <input type="text" 
                   id="address" 
                   name="address" required>

            <label for="city">City:</label>
            <input type="text" 
                   id="city" 
                   name="city" required>

            <label for="state">State:</label>
            <input type="text" 
                   id="state" 
                   name="state" required>

            <label for="zip">Zip Code:</label>
            <input type="text" 
                   id="zip"
                   name="zip" required>

            <h2>Payment Information</h2>
            <label for="cardholder">Name on Card:</label>
            <input type="text" id="cardholder" 
                   name="cardholder" required>

            <label for="cardnumber">Card Number:</label>
            <input type="text" 
                   id="cardnumber" 
                   name="cardnumber" required 
                   pattern="\d{4}-?\d{4}-?\d{4}-?\d{4}" required=>


            <label for="expmonth">Expiration Month:</label>
            <input type="text" 
                   id="expmonth" 
                   name="expmonth" required>

            <label for="expyear">Expiration Year:</label>
            <input type="text" 
                   id="expyear" 
                   name="expyear" required>

            <label for="cvv">CVV:</label>
            <input type="text" 
                   id="cvv"
                   name="cvv" required>

            <input type="submit" 
                   value="Place Order">
        </form>
    </section>  
</body>

</html>