<?php

session_start();

require_once '../../config/database.php';
require_once '../../admin/auth_check.php';




header('Content-Type:application/json');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');
header('Access-Control-Allow-Headers:Content-Type');

if ($_SERVER['REQUEST_METHOD']  === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$response = array();
$response['success'] =false;
$response['message'] = '';

try {
    $httpmethod = $_SERVER['REQUEST_METHOD'];

    if (!isset($pdo)) {
        throw new Exception('error in connecting the  Database');
    }

    if ($httpmethod === 'GET') {
        if (isset($_GET['id'])) {
             $product_id = $_GET['id'];
            $sql = "SELECT * FROM products WHERE id = ?";
              $stmt = $pdo->prepare($sql);
            $stmt->execute([$product_id]);
             $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
            $response['success'] =true;
              $response['product'] =  $product;
            } else {
                throw new Exception('Product not found');
            }
        } else {
          $sql = "SELECT * FROM products ORDER BY id DESC";
           $stmt = $pdo->query($sql);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response['success'] = true;
             $response['products'] = $products;
        }
    } 


    else if ($httpmethod === 'POST') {
        $newproductdata = $_POST;

    $required_fields = array('name', 'description', 'price', 'image_url', 'category', 'stock_quantity');
        foreach ($required_fields as $field) {
            if (empty($newproductdata[$field])) {
            throw new Exception("This $field field is required");
            }
        }

        $sql = "INSERT INTO products (name, description, price, image_url, category, stock_quantity) 
                  VALUES (:name, :description, :price, :image_url, :category, :stock_quantity)";
          $stmt = $pdo->prepare($sql);

        $success = $stmt->execute([
             'name' => $newproductdata['name'],
            'description' => $newproductdata['description'],
             'price' => $newproductdata['price'],
            'image_url' => $newproductdata['image_url'],
             'category' => $newproductdata['category'],
            'stock_quantity' => $newproductdata['stock_quantity']
        ]);

        if ($success) {
             $response['success'] = true;
          $response['message'] =  'Product has been created successfully';
                $response['product_id'] = $pdo->lastInsertId();
        } else {
            throw new Exception('could not create product');
        }
    }
    else if ($httpmethod === 'PUT') {
    
         $updatedproductdata = file_get_contents("php://input");
        parse_str($updatedproductdata, $newproductdata);

      
        if (empty($newproductdata['id'])) {
            throw new Exception('Product ID is required');
        }
        
        
        $update_fields = array();
         $update_values = array();
        $allowed_fields = array('name','description','price', 'image_url',  'category','stock_quantity');

          foreach ($allowed_fields as $field) {
            if (isset($newproductdata[$field])) {
            $update_fields[] = "$field = :$field";
                $update_values[$field] = $newproductdata[$field];
            }
        }                               
        if (empty($update_fields)) {
            throw new Exception('No fields to update');
        }
 
        $update_values['id'] = $newproductdata['id'];
          $sql = "UPDATE products SET " . implode(', ', $update_fields) . " WHERE id = :id";
        
         $stmt = $pdo->prepare($sql);
         $success = $stmt->execute($update_values);

        if ($success) {
             $response['success'] = true;
            $response['message'] = 'Product has been updated successfully';
        } else {
            throw new Exception('Failed to update the product');
        }
    } 
    else if ($httpmethod === 'DELETE') {
         $deleterequestdata = file_get_contents("php://input");
        parse_str($deleterequestdata, $newproductdata);
           if (empty($newproductdata['id'])) {
            throw new Exception('Product ID is required');
        }

    $sql = "DELETE FROM products WHERE id = ?";
         $stmt = $pdo->prepare($sql);
          $success = $stmt->execute([$data['id']]);

    if ($success) {
       $response['success'] = true;
             $response['message'] = 'Product has been deleted successfully';
                    } else {
       throw new Exception('Failed to delete product');
        }
    }
    else {
     throw new Exception('Method is not allowed');
    }
} catch (Exception $e) {
     http_response_code(400);
$response['success'] = false;
    $response['message'] = $e->getMessage();
     $response['error_details'] = array(
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    );
}
while (ob_get_level() > 0) {
    ob_end_clean();
}

echo json_encode($response);
exit();