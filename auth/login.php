<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request method'], 405);
}

// Verify CSRF token
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    json_response(['success' => false, 'message' => 'Invalid security token'], 403);
}

// Get and sanitize input
$username = sanitize_input($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validation
if (empty($username)) {
    json_response(['success' => false, 'message' => 'Username is required'], 400);
}

if (empty($password)) {
    json_response(['success' => false, 'message' => 'Password is required'], 400);
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get user by username or email
    $stmt = $db->prepare("SELECT id, username, email, password, full_name, role FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        json_response(['success' => false, 'message' => 'Invalid username or password'], 401);
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        json_response(['success' => false, 'message' => 'Invalid username or password'], 401);
    }

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['user_role'] = $user['role'];

    json_response([
        'success' => true, 
        'message' => 'Login successful!',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'role' => $user['role']
        ]
    ]);

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error. Please try again.'], 500);
}
?>
