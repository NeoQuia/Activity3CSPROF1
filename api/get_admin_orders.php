<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!is_logged_in() || !is_admin()) {
    json_response(['success' => false, 'message' => 'Unauthorized'], 401);
}

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get all orders with customer information
    $stmt = $db->prepare("
        SELECT 
            o.id,
            o.order_number,
            o.total_amount,
            o.status,
            o.shipping_address,
            o.created_at,
            u.full_name as customer_name,
            u.email as customer_email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
