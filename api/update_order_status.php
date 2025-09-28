<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!is_logged_in() || !is_admin()) {
    json_response(['success' => false, 'message' => 'Unauthorized'], 401);
}

// Check if required data is provided
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    json_response(['success' => false, 'message' => 'Missing required data'], 400);
}

$order_id = (int)$_POST['order_id'];
$status = $_POST['status'];

// Validate status
$valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
if (!in_array($status, $valid_statuses)) {
    json_response(['success' => false, 'message' => 'Invalid status'], 400);
}

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Start transaction
    $db->beginTransaction();

    // Update order status
    $stmt = $db->prepare("
        UPDATE orders 
        SET status = ? 
        WHERE id = ?
    ");
    $stmt->execute([$status, $order_id]);

    // Get order details for notification
    $stmt = $db->prepare("
        SELECT o.order_number, o.user_id 
        FROM orders o 
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Create notification for user
        $stmt = $db->prepare("
            INSERT INTO notifications (user_id, title, message, type) 
            VALUES (?, 'Order Status Updated', 'Your order #{$order['order_number']} status has been updated to {$status}', 'order_status')
        ");
        $stmt->execute([$order['user_id']]);
    }

    // Commit transaction
    $db->commit();

    json_response([
        'success' => true,
        'message' => 'Order status updated successfully'
    ]);

} catch (PDOException $e) {
    // Rollback transaction on error
    if ($db->inTransaction()) {
        $db->rollback();
    }
    json_response(['success' => false, 'message' => 'Database error'], 500);
}
?>
