<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    json_response(['success' => false, 'message' => 'Unauthorized'], 401);
}

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get user's orders with items
    $stmt = $db->prepare("
        SELECT 
            o.id,
            o.order_number,
            o.total_amount,
            o.status,
            o.shipping_address,
            o.created_at
        FROM orders o
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get items for each order
    foreach ($orders as &$order) {
        $stmt = $db->prepare("
            SELECT 
                oi.quantity,
                oi.price,
                p.name
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$order['id']]);
        $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    json_response([
        'success' => true,
        'data' => [
            'orders' => $orders
        ]
    ]);

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error'], 500);
}
?>
