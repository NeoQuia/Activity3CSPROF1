<?php
// Simple database connection test
echo "<h2>Database Connection Test</h2>";

// Test 1: Check if required files exist
echo "<h3>1. File Check:</h3>";
if (file_exists('config/database.php')) {
    echo "✅ config/database.php exists<br>";
} else {
    echo "❌ config/database.php missing<br>";
}

if (file_exists('includes/functions.php')) {
    echo "✅ includes/functions.php exists<br>";
} else {
    echo "❌ includes/functions.php missing<br>";
}

// Test 2: Check PHP extensions
echo "<h3>2. PHP Extensions:</h3>";
if (extension_loaded('pdo')) {
    echo "✅ PDO extension loaded<br>";
} else {
    echo "❌ PDO extension not loaded<br>";
}

if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO MySQL extension loaded<br>";
} else {
    echo "❌ PDO MySQL extension not loaded<br>";
}

// Test 3: Try to include and test database connection
echo "<h3>3. Database Connection Test:</h3>";
try {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "✅ Database connection successful!<br>";
        
        // Test if we can query the database
        $stmt = $db->query("SELECT 1 as test");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            echo "✅ Database query test successful<br>";
        } else {
            echo "❌ Database query test failed<br>";
        }
        
        // Test if tables exist
        echo "<h3>4. Table Check:</h3>";
        $tables = ['users', 'products', 'analytics'];
        foreach ($tables as $table) {
            $stmt = $db->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            if ($stmt->fetch()) {
                echo "✅ Table '$table' exists<br>";
            } else {
                echo "❌ Table '$table' does not exist<br>";
            }
        }
        
        // Check if users table has data
        echo "<h3>5. Data Check:</h3>";
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Users in database: " . $result['count'] . "<br>";
        
    } else {
        echo "❌ Database connection failed - no connection object returned<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "<br>";
} catch (Exception $e) {
    echo "❌ General error: " . $e->getMessage() . "<br>";
}

// Test 4: Show current database configuration
echo "<h3>6. Current Configuration:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current working directory: " . getcwd() . "<br>";

// Test 5: Session test
echo "<h3>7. Session Test:</h3>";
session_start();
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . (session_status() == PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "<br>";

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "1. If database connection fails, check your credentials in config/database.php<br>";
echo "2. If tables don't exist, import the database_setup_awardspace.sql file<br>";
echo "3. Make sure you're using the correct database name format for AwardSpace<br>";
?>
