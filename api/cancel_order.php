<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    json_response(['success' => false, 'message' => 'Unauthorized'], 401);
}

// Check if order ID is provided
if (!isset($_POST['order_id'])) {
    json_response(['success' => false, 'message' => 'Order ID required'], 400);
}

$order_id = (int)$_POST['order_id'];

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Start transaction
    $db->beginTransaction();

    // Check if order exists and belongs to user
    $stmt = $db->prepare("
        SELECT id, status, user_id 
        FROM orders 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        json_response(['success' => false, 'message' => 'Order not found'], 404);
    }

    if ($order['status'] !== 'pending') {
        json_response(['success' => false, 'message' => 'Only pending orders can be cancelled'], 400);
    }

    // Update order status to cancelled
    $stmt = $db->prepare("
        UPDATE orders 
        SET status = 'cancelled' 
        WHERE id = ?
    ");
    $stmt->execute([$order_id]);

    // Restore product quantities
    $stmt = $db->prepare("
        UPDATE products p
        JOIN order_items oi ON p.id = oi.product_id
        SET p.quantity = p.quantity + oi.quantity
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);

    // Update product status if needed
    $stmt = $db->prepare("
        UPDATE products p
        JOIN order_items oi ON p.id = oi.product_id
        SET p.status = 'Available'
        WHERE oi.order_id = ? AND p.quantity > 0
    ");
    $stmt->execute([$order_id]);

    // Create notification for user
    $stmt = $db->prepare("
        INSERT INTO notifications (user_id, title, message, type) 
        VALUES (?, 'Order Cancelled', 'Your order has been cancelled', 'order_status')
    ");
    $stmt->execute([$_SESSION['user_id']]);

    // Commit transaction
    $db->commit();

    json_response([
        'success' => true,
        'message' => 'Order cancelled successfully'
    ]);

} catch (PDOException $e) {
    // Rollback transaction on error
    if ($db->inTransaction()) {
        $db->rollback();
    }
    json_response(['success' => false, 'message' => 'Database error'], 500);
}
?>
