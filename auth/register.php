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
$email = sanitize_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$full_name = sanitize_input($_POST['full_name'] ?? '');

// Validation
$errors = [];

if (empty($username)) {
    $errors[] = 'Username is required';
} elseif (strlen($username) < 3) {
    $errors[] = 'Username must be at least 3 characters';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!validate_email($email)) {
    $errors[] = 'Invalid email format';
}

if (empty($password)) {
    $errors[] = 'Password is required';
} elseif (!validate_password($password)) {
    $errors[] = 'Password must be at least 6 characters';
}

if ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match';
}

if (empty($full_name)) {
    $errors[] = 'Full name is required';
}

if (!empty($errors)) {
    json_response(['success' => false, 'message' => implode(', ', $errors)], 400);
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if username already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        json_response(['success' => false, 'message' => 'Username already exists'], 400);
    }

    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        json_response(['success' => false, 'message' => 'Email already exists'], 400);
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$username, $email, $hashed_password, $full_name]);

    if ($result) {
        json_response(['success' => true, 'message' => 'Registration successful! Please login.']);
    } else {
        json_response(['success' => false, 'message' => 'Registration failed. Please try again.'], 500);
    }

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error. Please try again.'], 500);
}
?>
