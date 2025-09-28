<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    json_response(['success' => false, 'message' => 'Unauthorized'], 401);
}

// Check if required data is provided
if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    json_response(['success' => false, 'message' => 'Missing required data'], 400);
}

$product_id = (int)$_POST['product_id'];
$quantity = (int)$_POST['quantity'];

if ($product_id <= 0 || $quantity <= 0) {
    json_response(['success' => false, 'message' => 'Invalid product or quantity'], 400);
}

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if product exists and is available
    $stmt = $db->prepare("
        SELECT id, quantity, status, name 
        FROM products 
        WHERE id = ? AND status = 'Available'
    ");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        json_response(['success' => false, 'message' => 'Product not found or not available'], 404);
    }

    if ($product['quantity'] < $quantity) {
        json_response(['success' => false, 'message' => 'Insufficient stock. Available: ' . $product['quantity']], 400);
    }

    // Check if item already exists in cart
    $stmt = $db->prepare("
        SELECT id, quantity 
        FROM cart 
        WHERE user_id = ? AND product_id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    $existing_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing_item) {
        // Update existing item
        $new_quantity = $existing_item['quantity'] + $quantity;
        
        if ($new_quantity > $product['quantity']) {
            json_response(['success' => false, 'message' => 'Cannot add more items. Total would exceed stock.'], 400);
        }

        $stmt = $db->prepare("
            UPDATE cart 
            SET quantity = ? 
            WHERE id = ?
        ");
        $stmt->execute([$new_quantity, $existing_item['id']]);
        
        json_response([
            'success' => true, 
            'message' => 'Cart updated successfully',
            'data' => ['new_quantity' => $new_quantity]
        ]);
    } else {
        // Add new item to cart
        $stmt = $db->prepare("
            INSERT INTO cart (user_id, product_id, quantity) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
        
        json_response([
            'success' => true, 
            'message' => 'Product added to cart successfully'
        ]);
    }

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error'], 500);
}
?>
