<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    json_response(['success' => false, 'message' => 'Unauthorized'], 401);
}

// Check if required data is provided
if (!isset($_POST['items']) || !isset($_POST['first_name']) || !isset($_POST['last_name']) || 
    !isset($_POST['email']) || !isset($_POST['phone']) || !isset($_POST['address']) || 
    !isset($_POST['city']) || !isset($_POST['zip_code'])) {
    json_response(['success' => false, 'message' => 'Missing required data'], 400);
}

$items = json_decode($_POST['items'], true);
if (empty($items)) {
    json_response(['success' => false, 'message' => 'No items in cart'], 400);
}

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Start transaction
    $db->beginTransaction();

    // Generate order number
    $order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Calculate total amount
    $total_amount = 0;
    foreach ($items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    // Create shipping address string
    $shipping_address = $_POST['address'] . ', ' . $_POST['city'] . ', ' . $_POST['zip_code'];

    // Insert order
    $stmt = $db->prepare("
        INSERT INTO orders (order_number, user_id, total_amount, shipping_address) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$order_number, $_SESSION['user_id'], $total_amount, $shipping_address]);
    $order_id = $db->lastInsertId();

    // Insert order items and update product quantities
    foreach ($items as $item) {
        // Insert order item
        $stmt = $db->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);

        // Update product quantity
        $stmt = $db->prepare("
            UPDATE products 
            SET quantity = quantity - ? 
            WHERE id = ?
        ");
        $stmt->execute([$item['quantity'], $item['product_id']]);

        // Check if product is now out of stock
        $stmt = $db->prepare("SELECT quantity FROM products WHERE id = ?");
        $stmt->execute([$item['product_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product['quantity'] <= 0) {
            $stmt = $db->prepare("UPDATE products SET status = 'Sold Out' WHERE id = ?");
            $stmt->execute([$item['product_id']]);
        }
    }

    // Clear user's cart
    $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    // Create notification for admin
    $stmt = $db->prepare("
        INSERT INTO notifications (user_id, title, message, type) 
        SELECT id, 'New Order', 'Order #{$order_number} has been placed', 'new_order'
        FROM users WHERE role = 'admin'
    ");
    $stmt->execute();

    // Create notification for user
    $stmt = $db->prepare("
        INSERT INTO notifications (user_id, title, message, type) 
        VALUES (?, 'Order Confirmed', 'Your order #{$order_number} has been confirmed', 'order_status')
    ");
    $stmt->execute([$_SESSION['user_id']]);

    // Add analytics entry for income
    $stmt = $db->prepare("
        INSERT INTO analytics (type, amount, description, date) 
        VALUES ('income', ?, 'Order #{$order_number}', CURDATE())
    ");
    $stmt->execute([$total_amount]);

    // Commit transaction
    $db->commit();

    json_response([
        'success' => true,
        'message' => 'Order placed successfully',
        'data' => [
            'order_id' => $order_id,
            'order_number' => $order_number,
            'total_amount' => $total_amount
        ]
    ]);

} catch (PDOException $e) {
    // Rollback transaction on error
    if ($db->inTransaction()) {
        $db->rollback();
    }
    json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
}
?>
