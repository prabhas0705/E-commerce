# 🛒 E-Commerce Web Application

A lightweight, modular e-commerce platform developed with **PHP**, **MySQL**, **HTML/CSS**, and **JavaScript**. The application supports product browsing, cart management, checkout, user login, and admin features — perfect for small online shops or learning projects.

---

## 📦 Features

- 🔐 User registration & login  
- 🛍️ Product catalog with dynamic listings  
- 🛒 Add to cart & wishlist functionality  
- 💳 Checkout flow with total calculation  
- 🧑‍💼 Admin dashboard for product management  
- 📁 MySQL-based storage for users, products, and transactions  
- 🔄 Edit/delete products with real-time feedback  
- 🎯 Clean and responsive UI with basic validation

---

## 🛠️ Tech Stack

| Layer       | Technology                  |
|-------------|-----------------------------|
| Frontend    | HTML, CSS, JavaScript       |
| Backend     | PHP                         |
| Database    | MySQL (`ecommerce.sql`)     |
| Tools       | XAMPP / WAMP / PHPMyAdmin   |

---

## 📂 Project Structure

```bash
ecom/
├── css/
│   └── styles.css
├── js/
│   └── scripts.js
├── admin/
│   ├── add_product.php
│   ├── edit_product.php
│   └── admin_dashboard.php
├── cart.php
├── wishlist.php
├── checkout.php
├── index.php
├── login.php
├── register.php
├── logout.php
├── includes/
│   ├── db.php
│   └── header.php
└── database/
    └── ecommerce.sql
```

---

## 🧰 Installation Guide

1. 🧾 **Import SQL Database**  
   - Open `PHPMyAdmin`  
   - Create a new database (e.g. `ecommerce`)  
   - Import the `ecommerce.sql` file from the `/database` folder

2. 🛠 **Update Database Connection**  
   - Edit `/includes/db.php` with your DB credentials:
     ```php
     $conn = mysqli_connect("localhost", "root", "", "ecommerce");
     ```

3. 🚀 **Run Locally**  
   - Place the project folder in `htdocs/` (XAMPP) or `www/` (WAMP)
   - Start Apache & MySQL
   - Open `http://localhost/ecom/` in browser

---

## 🖼️ Screenshots

### 🏠 Main Page
![Main Page](https://github.com/user-attachments/assets/c1d6ad0b-a018-4f4d-8992-06d20e8b9c64)

### 🛠️ Admin Dashboard
![Admin Page](https://github.com/user-attachments/assets/e949d6c4-b160-4a8d-b1e3-584d2a05f4a9)

### ✏️ Edit Product
![Edit Product](https://github.com/user-attachments/assets/9cc9f57a-7d04-4b2b-9943-fee9ab5c0179)

### 🛒 Cart View
![Cart](https://github.com/user-attachments/assets/60569ef8-614e-48c2-8bf3-d88581e307a2)

### 💖 Wishlist
![Wishlist](https://github.com/user-attachments/assets/0c07aa8c-c173-4714-b782-708702045c05)

---
  
## 🙌 Acknowledgments

Inspired by classic e-commerce structure for educational and practical implementation using PHP + MySQL.

---

📬 For feedback or contributions, feel free to fork this repo and raise a PR!
