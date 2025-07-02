<?php
require_once 'auth_check.php';

$product_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if (!$product_id) {
    header('Location: products.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-item nav-link" href="products.php">Back to Products</a>
                <a class="nav-item nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Product</h4>
                    </div>
                    <div class="card-body">
                        <div id="alertContainer"></div>
                        <form id="editProductForm">
                            <input type="hidden" id="product_id" name="id">
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
                                <input type="url" class="form-control" id="image_url" name="image_url" required>
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
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Update Product</button>
                                <a href="products.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var productId = <?php echo json_encode($product_id); ?>;
        
        window.onload = function() {
            loadProduct(productId);
            
            var form = document.getElementById('editProductForm');
            form.onsubmit = function(e) {
                e.preventDefault();
                updateProduct(this);
            };
        };

        function loadProduct(id) {
            showAlert('info', 'Loading product data...');
            
            fetch('../api/products/index.php?id=' + id)
            .then(function(response) {
                if (!response.headers.get('content-type').includes('application/json')) {
                    throw new Error('Server returned wrong type of data');
                }
                
                if (!response.ok) {
                    throw new Error('Could not load product data');
                }
                return response.json();
            })
            .then(function(result) {
                if (result.success && result.product) {
                    var product = result.product;
                    
                    document.getElementById('product_id').value = product.id;
                    document.getElementById('name').value = product.name;
                    document.getElementById('description').value = product.description;
                    document.getElementById('price').value = product.price;
                    document.getElementById('image_url').value = product.image_url;
                    document.getElementById('category').value = product.category;
                    document.getElementById('stock_quantity').value = product.stock_quantity;
                    
                    
                    showAlert('success', 'Product data loaded successfully');
                } else {
                    throw new Error('Could not find product data');
                }
            })
            .catch(function(error) {
             
                showAlert('danger', 'Error: ' + error.message);
                console.log('Error in loading product:', error);
                
                setTimeout(function() {
                    window.location.href = 'products.php';
                }, 2000);
            });
        }

        function updateProduct(form) {
         
            showAlert('info', 'Updating product please wait.');
            
            var formData = new FormData(form);
            var data = {};
            formData.forEach(function(value, key) {
                data[key] = value;
            });
            
            fetch('../api/products/index.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(data).toString()
            })
            .then(function(response) {
                
                if (!response.headers.get('content-type').includes('application/json')) {
                    throw new Error('Server returned wrong type of data');
                }
                
                if (!response.ok) {
                    throw new Error('Could not update product');
                }
                
                // Get the JSON data
                return response.json();
            })
            .then(function(result) {
                
                if (result.success) {
                    showAlert('success', 'Product has been updated successfully');
                    
                    setTimeout(function() {
                        window.location.href = 'products.php';
                    }, 1500);
                } else {
                    throw new Error('Failed to update product');
                }
            })
            .catch(function(error) {
               
                showAlert('danger', 'Error: ' + error.message);
                console.log('Error updating product:', error);
            });
        }

       
        function showAlert(type, message) {
            var alertContainer = document.getElementById('alertContainer');
            alertContainer.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>';
        }
    </script>
</body>
</html>