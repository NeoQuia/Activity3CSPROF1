-- Database Setup for Furni House Dashboard
-- Run this SQL in your AwardSpace phpMyAdmin

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS furni_house_db;
USE furni_house_db;

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    status ENUM('Available', 'Sold Out') DEFAULT 'Available',
    image_url VARCHAR(255),
    rating DECIMAL(3,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Analytics table for income/spending tracking
CREATE TABLE analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('income', 'spending') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description VARCHAR(255),
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@furnihouse.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lubna Admin', 'admin'),
('user1', 'user1@furnihouse.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'user');

-- Insert sample products
INSERT INTO products (product_id, name, description, price, quantity, status, image_url, rating) VALUES
('555-012', 'Cupboard', 'Modern wooden cupboard with multiple compartments', 473.85, 106, 'Available', 'cupboard.jpg', 4.2),
('555-006', 'Standing Lamp', 'Elegant standing lamp with adjustable height', 630.44, 270, 'Available', 'lamp.jpg', 4.5),
('555-010', 'Cabinet', 'Compact cabinet for storage', 106.58, 120, 'Sold Out', 'cabinet.jpg', 4.0),
('555-0104', 'Dressing Table', 'Beautiful dressing table with mirror', 100.55, 105, 'Sold Out', 'dressing-table.jpg', 4.3),
('555-015', 'Minimalist Chair', 'Modern minimalist chair design', 120.00, 50, 'Available', 'chair.jpg', 4.5),
('555-016', 'Desk Lamp', 'Adjustable desk lamp with LED lighting', 200.00, 75, 'Available', 'desk-lamp.jpg', 4.5);

-- Cart table for shopping cart functionality
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Orders table for order management
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table for order details
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Notifications table for user notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('order_status', 'new_order', 'general') DEFAULT 'general',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample analytics data
INSERT INTO analytics (type, amount, description, date) VALUES
('income', 17000.00, 'Monthly income', CURDATE()),
('spending', 900.00, 'Monthly expenses', CURDATE()),
('income', 15000.00, 'Previous month income', DATE_SUB(CURDATE(), INTERVAL 1 MONTH)),
('spending', 1200.00, 'Previous month expenses', DATE_SUB(CURDATE(), INTERVAL 1 MONTH));
