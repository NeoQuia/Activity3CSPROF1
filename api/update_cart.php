<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    json_response(['success' => false, 'message' => 'Unauthorized'], 401);
}

// Check if required data is provided
if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
    json_response(['success' => false, 'message' => 'Missing required data'], 400);
}

$cart_id = (int)$_POST['cart_id'];
$quantity = (int)$_POST['quantity'];

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();

    if ($quantity <= 0) {
        // Remove item from cart
        $stmt = $db->prepare("
            DELETE FROM cart 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$cart_id, $_SESSION['user_id']]);
        
        json_response([
            'success' => true, 
            'message' => 'Item removed from cart'
        ]);
    } else {
        // Check stock availability
        $stmt = $db->prepare("
            SELECT p.quantity as stock_quantity, p.name
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.id = ? AND c.user_id = ?
        ");
        $stmt->execute([$cart_id, $_SESSION['user_id']]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            json_response(['success' => false, 'message' => 'Cart item not found'], 404);
        }

        if ($quantity > $item['stock_quantity']) {
            json_response(['success' => false, 'message' => 'Insufficient stock. Available: ' . $item['stock_quantity']], 400);
        }

        // Update quantity
        $stmt = $db->prepare("
            UPDATE cart 
            SET quantity = ? 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$quantity, $cart_id, $_SESSION['user_id']]);
        
        json_response([
            'success' => true, 
            'message' => 'Cart updated successfully'
        ]);
    }

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error'], 500);
}
?>
