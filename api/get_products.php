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

    // Get all products
    $stmt = $db->prepare("
        SELECT 
            product_id,
            name,
            price,
            quantity,
            status,
            rating,
            image_url
        FROM products 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get popular products (top 5 by rating)
    $stmt = $db->prepare("
        SELECT 
            product_id,
            name,
            price,
            rating,
            image_url
        FROM products 
        WHERE rating > 0
        ORDER BY rating DESC, created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $popular_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get statistics
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_products,
            SUM(CASE WHEN status = 'Available' THEN 1 ELSE 0 END) as available_products,
            SUM(CASE WHEN status = 'Sold Out' THEN 1 ELSE 0 END) as sold_out_products,
            AVG(price) as avg_price
        FROM products
    ");
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    json_response([
        'success' => true,
        'data' => [
            'products' => $products,
            'popular_products' => $popular_products,
            'stats' => [
                'total_products' => $stats['total_products'],
                'available_products' => $stats['available_products'],
                'sold_out_products' => $stats['sold_out_products'],
                'avg_price' => number_format($stats['avg_price'], 2)
            ]
        ]
    ]);

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error'], 500);
}
?>
