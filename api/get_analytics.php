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

    // Get current month income and spending
    $stmt = $db->prepare("
        SELECT 
            SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
            SUM(CASE WHEN type = 'spending' THEN amount ELSE 0 END) as total_spending
        FROM analytics 
        WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())
    ");
    $stmt->execute();
    $current_month = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get previous month income and spending
    $stmt = $db->prepare("
        SELECT 
            SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
            SUM(CASE WHEN type = 'spending' THEN amount ELSE 0 END) as total_spending
        FROM analytics 
        WHERE MONTH(date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) 
        AND YEAR(date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
    ");
    $stmt->execute();
    $previous_month = $stmt->fetch(PDO::FETCH_ASSOC);

    // Calculate percentage changes
    $income_change = 0;
    $spending_change = 0;

    if ($previous_month['total_income'] > 0) {
        $income_change = (($current_month['total_income'] - $previous_month['total_income']) / $previous_month['total_income']) * 100;
    }

    if ($previous_month['total_spending'] > 0) {
        $spending_change = (($current_month['total_spending'] - $previous_month['total_spending']) / $previous_month['total_spending']) * 100;
    }

    // Get weekly data for chart
    $stmt = $db->prepare("
        SELECT 
            DATE(date) as date,
            SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN type = 'spending' THEN amount ELSE 0 END) as spending
        FROM analytics 
        WHERE date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(date)
        ORDER BY date
    ");
    $stmt->execute();
    $weekly_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get sales data from orders
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_orders,
            SUM(total_amount) as total_sales,
            AVG(total_amount) as avg_order_value
        FROM orders 
        WHERE status != 'cancelled'
    ");
    $stmt->execute();
    $sales_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get best-selling products
    $stmt = $db->prepare("
        SELECT 
            p.name,
            p.product_id,
            SUM(oi.quantity) as total_sold,
            SUM(oi.quantity * oi.price) as total_revenue
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.status != 'cancelled'
        GROUP BY p.id, p.name, p.product_id
        ORDER BY total_sold DESC
        LIMIT 5
    ");
    $stmt->execute();
    $best_selling = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get order status distribution
    $stmt = $db->prepare("
        SELECT 
            status,
            COUNT(*) as count
        FROM orders 
        GROUP BY status
    ");
    $stmt->execute();
    $order_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

    json_response([
        'success' => true,
        'data' => [
            'current_month' => [
                'income' => number_format($current_month['total_income'], 2),
                'spending' => number_format($current_month['total_spending'], 2)
            ],
            'changes' => [
                'income' => round($income_change, 1),
                'spending' => round($spending_change, 1)
            ],
            'weekly_data' => $weekly_data,
            'sales_stats' => [
                'total_orders' => $sales_stats['total_orders'] ?? 0,
                'total_sales' => number_format($sales_stats['total_sales'] ?? 0, 2),
                'avg_order_value' => number_format($sales_stats['avg_order_value'] ?? 0, 2)
            ],
            'best_selling' => $best_selling,
            'order_status' => $order_status
        ]
    ]);

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error'], 500);
}
?>
