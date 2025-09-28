<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    json_response(['success' => false, 'message' => 'Unauthorized'], 401);
}

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    json_response(['success' => false, 'message' => 'Order ID required'], 400);
}

$order_id = (int)$_GET['order_id'];

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get order details
    $stmt = $db->prepare("
        SELECT 
            o.id,
            o.order_number,
            o.total_amount,
            o.status,
            o.shipping_address,
            o.created_at
        FROM orders o
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        json_response(['success' => false, 'message' => 'Order not found'], 404);
    }

    // Get order items
    $stmt = $db->prepare("
        SELECT 
            oi.quantity,
            oi.price,
            p.name,
            p.image_url
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    json_response([
        'success' => true,
        'data' => [
            'order' => $order,
            'items' => $items
        ]
    ]);

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error'], 500);
}
?>
