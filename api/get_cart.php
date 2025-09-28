<?php
session_start();
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

    // Get cart items with product details
    $stmt = $db->prepare("
        SELECT 
            c.id as cart_id,
            c.quantity,
            p.id as product_id,
            p.product_id as product_code,
            p.name,
            p.price,
            p.image_url,
            p.quantity as stock_quantity,
            p.status
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate totals
    $subtotal = 0;
    $total_items = 0;
    
    foreach ($items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
        $total_items += $item['quantity'];
    }

    json_response([
        'success' => true,
        'data' => [
            'items' => $items,
            'subtotal' => number_format($subtotal, 2),
            'total_items' => $total_items
        ]
    ]);

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error'], 500);
}
?>
