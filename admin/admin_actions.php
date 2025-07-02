<?php

session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Please login first']));
}

if (!$_SESSION['is_admin']) {
    die(json_encode(['error' => 'Only admin can access this page']));
}

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

$response = array();
$response['success'] = false;
$response['message'] = '';

try {
    
    if ($action == 'add_product') {
     
        $name = '';
        if (isset($_POST['name'])) {
            $name = trim($_POST['name']);
         }
        
        $description = '';
        if (isset($_POST['description'])) {
            $description = trim($_POST['description']);
        }
        
        $price = '';
        if (isset($_POST['price'])) {
            $price = trim($_POST['price']);}
        
        $image_url = '';
        if (isset($_POST['image_url'])) {
            $image_url = trim($_POST['image_url']);  }
        
        $category = '';
        if (isset($_POST['category'])) {
            $category = trim($_POST['category']);  }
        
        $stock_quantity = '';
        if (isset($_POST['stock_quantity'])) {
            $stock_quantity = trim($_POST['stock_quantity']);}

        // Need to check if the fields are filled or not!!
        $errors = array();
        if ($name == '') {
            $errors[ ] = "Product name is required";
         }
        if ($description == '') {
            $errors[] = "Product Description is required";
          }
        if ($price == '' || !is_numeric($price) || $price <= 0) {
            $errors[ ] = "please enter correct price";
        }
        if ($image_url == '') {
            $errors[ ] = "Image URL is required";}
        if ($category == '') {
            $errors[ ] = "product Category is required";  }
        if ($stock_quantity == '' || !is_numeric($stock_quantity) || $stock_quantity < 0) {
            $errors[] = "Valid stock quantity is required";  }

        // If no errors are found then add product
        if (count($errors) == 0) {
            $sql = "INSERT INTO products (name, description, price, image_url, category, stock_quantity) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$name, $description, $price, $image_url, $category, $stock_quantity])) {
                $response['success'] = true;
                $response['message'] = 'Product has been added successfully';
                $response['product_id'] = $pdo->lastInsertId();
            }
        } else {
            $response['success' ] = false;
            $response['errors'] = $errors;
        }
    }



    else if ($action == 'edit_product') {
        $product_id = null;
        if (isset($_GET[ 'id'])) {
            $product_id = $_GET['id'];
        }
        

        if (!$product_id) {
            throw new Exception('Product ID is required');
        }

    

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $sql = "SELECT * FROM products WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product) {
                $response[ 'success'] = true;
                $response['product'] = $product;
            } else {
                throw new Exception('Product is not found');
            }
        }
        



        else {
            
            $required = array('product_id', 'name', 'description', 'price', 'category', 'stock_quantity');
            foreach ($required as $field) {
                if (!isset($_POST[$field]) || $_POST[$field] == '') {
                    throw new Exception("$field is required");
                }
            }

           

            $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
             $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
             $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
            $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
            $image_url = filter_var($_POST['image_url'], FILTER_VALIDATE_URL);
            $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
            $stock_quantity = filter_var($_POST['stock_quantity'], FILTER_VALIDATE_INT);

            



            if (!$product_id) {
             throw new Exception(' product ID is not valid');
            }
            if ($price === false || $price < 0) {
                throw new Exception('Please enter valid price');
            }
            if ($stock_quantity === false || $stock_quantity < 0) {
            throw new Exception('Invalid stock quantity');
            }

            // Update the product to database 
            $sql = "UPDATE products SET 
                 name = :name,
                 description = :description,
                price = :price,
                image_url = :image_url,
               category = :category,
                stock_quantity = :stock_quantity
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
                'name' => $name,
                 'description' => $description,
                 'price' => $price,
                 'image_url' => $image_url,
               'category' => $category,
               'stock_quantity' => $stock_quantity,
                 'id' => $product_id
            ]);

            if ($success) {
             $response['success'] = true;
            $response['message'] = 'Product has been updated successfully';
            } else {
             throw new Exception('Failed to update product');
            }
        }
    }
    else if ($action == 'delete_product') {
       
        $product_id = null;
        if (isset($_POST['id'])) {
         $product_id = $_POST['id'];
        }
        
        if (!$product_id) {
        throw new Exception('Product ID is required');
        }
          
         
        $sql = "SELECT id FROM products WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id]);
        if (!$stmt->fetch()) {
          throw new Exception('Product not found');
        }
    $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$product_id]);

        if ($success) {
          $response['success'] = true;
         $response['message'] = 'Product has been deleted successfully';
        } else {
            throw new Exception('Failed to delete product');
        }
    }
    else if ($action == 'get_products') {
       
         $sql = "SELECT * FROM products ORDER BY id DESC";
          $stmt = $pdo->query($sql);
         $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
         $response['success'] = true;
        $response['products'] = $products;
    }
    else {
        throw new Exception('Invalid action');
    }
} catch (Exception $e) {
   $response['success'] = false;
    $response['message'] = $e->getMessage();
}                         

header('Content-Type: application/json');
echo json_encode($response);