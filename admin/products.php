<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$errors = [];
$success = '';

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <span class = "navbar-text text-light ms-3">Welcome Admin</span>
            <div class="navbar-nav ms-auto">
            <a class="nav-item nav-link" href="../index.php">View Site</a>
            <a class="nav-item nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Add New Product</h4>
                    </div>
                    <div class="card-body">
                    <div id = "alertContainer"></div>

                        <form id= "addProductForm" method = "POST" action = "admin_actions.php?action=add_product" enctype="multipart/form-data">
                            <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                            </div>
                            <div class="mb-3">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" class="form-control" id="image_url" name="image_url">
                        </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="">Select a category</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Clothing">Clothing</option>
                                <option value="Home & Kitchen">Home & Kitchen</option>
                                <option value="Books">Books</option>
                                <option value="Sports & Outdoors">Sports & Outdoors</option>
                                <option value="Toys & Games">Toys & Games</option>
                                <option value="Beauty & Personal Care">Beauty & Personal Care</option>
                            </select>
                            </div>
                            <div class="mb-3">
                                <label for="stock_quantity" class="form-label">Stock Quantity</label>
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Product</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Product List</h4>
                    </div>
                    <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                    <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name of the Product</th>
                                <th>Price</th>
                            <th>Category</th>
                                <th>Stock</th>
                            <th>Actions</th>
                            </tr>
                                </thead>
                    <tbody id = "productList"></tbody>
                    </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
      
  document.addEventListener('DOMContentLoaded', function() {
          
        loadProducts();
        
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
                
                e.preventDefault();
                
                 var formData = new FormData(this);
                  
                 fetch('admin_actions.php?action=add_product', {
                       method: 'POST',
                    body: formData
                 })
                 .then(function(response) {
                    return response.json();
                 })
                .then(function(result) {
                     if (result.success) {
                    showAlert('success', result.message);
                   loadProducts();
                    } else {
                     showAlert('danger', result.errors.join('<br>'));
                    }
                 })
                .catch(function(error) {
                     showAlert('danger', 'Error adding product');
                });
              });
            });

        function loadProducts() {
            
             fetch('admin_actions.php?action=get_products')
             .then(function(response) {
                return response.json();
            })
            .then(function(result) {
                if (result.success) {
                     var productList = document.getElementById('productList');
                    var html = '';
                    
                    for (var i = 0; i < result.products.length; i++) {
                        var product = result.products[i];
                        html += '<tr>';
                        html += '<td>' + product.id + '</td>';
                         html += '<td>' + product.name + '</td>';
                        html += '<td>₹' + Number(product.price).toFixed(2) + '</td>';
                        html += '<td>' + product.category + '</td>';
                         html += '<td>' + product.stock_quantity + '</td>';
                         html += '<td>';
                         html += '<a href="edit_Product.php?id=' + product.id + '" class="btn btn-sm btn-primary">';
                        html += '<i class="bi bi-pencil"></i>';
                         html += '</a>';
                         html += '<button onclick="deleteProduct(' + product.id + ')" class="btn btn-sm btn-danger">';
                         html += '<i class="bi bi-trash"></i>';
                          html += '</button>';
                        html += '</td>';
                        html += '</tr>';
                    }
                    
                    productList.innerHTML = html;
                }
            })
            .catch(function(error) {
                showAlert('danger', 'Error in loading the products');
            });
        }
    function deleteProduct(id) {
            
            if (!confirm('Are you sure that you want to delete this product?')) {
                return; }
            
            var formData = new FormData();
            formData.append('id', id);
            
            fetch('admin_actions.php?action=delete_product', {
                 method: 'POST',
                body: formData })
            .then(function(response) {
                return response.json();
            })
            .then(function(result) {
            if (result.success) {
             showAlert('success', 'product deleted successfully');
                    location.reload();
            } 
            else 
            {
                showAlert('danger', result.message || 'could not delete product');
                }
            })
            .catch(function(error) 
            {
             console.error('Error:', error);
            showAlert('danger', 'Error deleting product');
            });
        }

        function showAlert(type, message) {
           var alertDiv = document.createElement('div');
             alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show';
              alertDiv.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
             document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.container').firstChild);
        }
                 
    </script>
</body>
</html>






    