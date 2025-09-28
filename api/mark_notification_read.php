<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    json_response(['success' => false, 'message' => 'Unauthorized'], 401);
}

// Check if notification ID is provided
if (!isset($_POST['notification_id'])) {
    json_response(['success' => false, 'message' => 'Notification ID required'], 400);
}

$notification_id = (int)$_POST['notification_id'];

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();

    // Mark notification as read
    $stmt = $db->prepare("
        UPDATE notifications 
        SET is_read = 1 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$notification_id, $_SESSION['user_id']]);

    json_response([
        'success' => true,
        'message' => 'Notification marked as read'
    ]);

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error'], 500);
}
?>
