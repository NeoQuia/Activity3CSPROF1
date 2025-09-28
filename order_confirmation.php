<?php
require_once 'includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('index.php');
}

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    redirect('shop.php');
}

$user_name = $_SESSION['full_name'] ?? $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furni House - Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .confirmation-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff6b6b;
        }
        
        .logo i {
            font-size: 2rem;
            margin-right: 0.5rem;
        }
        
        .nav-menu {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin: 0.25rem 1rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #6c757d;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            color: white;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }
        
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            margin: 0;
            color: #333;
            font-weight: 700;
        }
        
        .header p {
            margin: 0.5rem 0 0 0;
            color: #6c757d;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 107, 107, 0.1);
            border-radius: 10px;
            color: #ff6b6b;
            cursor: pointer;
            position: relative;
        }
        
        .user-profile:hover {
            background: rgba(255, 107, 107, 0.2);
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            min-width: 150px;
            z-index: 1000;
            display: none;
            margin-top: 0.5rem;
        }
        
        .dropdown-menu.show {
            display: block;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .confirmation-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .success-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #28a745, #20c997);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: white;
            font-size: 2.5rem;
        }
        
        .success-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .success-message {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        
        .order-details {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .details-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #333;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #333;
        }
        
        .detail-value {
            color: #6c757d;
        }
        
        .order-items {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #333;
        }
        
        .item-price {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .item-total {
            font-weight: 600;
            color: #ff6b6b;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
            color: white;
        }
        
        .btn-outline-primary {
            border: 2px solid #ff6b6b;
            color: #ff6b6b;
            background: transparent;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: #ff6b6b;
            color: white;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-home"></i>
                    Furni House
                </div>
            </div>
            
            <nav class="nav-menu">
                <div class="nav-item">
                    <a href="shop.php" class="nav-link">
                        <i class="fas fa-store"></i>
                        Shop
                    </a>
                </div>
                <div class="nav-item">
                    <a href="cart.php" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        Cart
                    </a>
                </div>
                <div class="nav-item">
                    <a href="orders.php" class="nav-link active">
                        <i class="fas fa-box"></i>
                        My Orders
                    </a>
                </div>
                <div class="nav-item">
                    <a href="notifications.php" class="nav-link">
                        <i class="fas fa-bell"></i>
                        Notifications
                    </a>
                </div>
                <?php if (is_admin()): ?>
                <div class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Admin Dashboard
                    </a>
                </div>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1>Order Confirmation</h1>
                        <p>Thank you for your order!</p>
                    </div>
                    <div class="col-md-6">
                        <div class="header-actions justify-content-end">
                            <div class="user-profile dropdown">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                                </div>
                                <span>Hi, <?php echo $user_name; ?></span>
                                <i class="fas fa-chevron-down"></i>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="orders.php">
                                        <i class="fas fa-box me-2"></i>My Orders
                                    </a>
                                    <a class="dropdown-item" href="auth/logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Content -->
            <div class="confirmation-content">
                <div class="success-card">
                    <div class="success-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <h2 class="success-title">Order Confirmed!</h2>
                    <p class="success-message">
                        Your order has been successfully placed. You will receive a confirmation email shortly.
                    </p>
                </div>

                <div class="order-details">
                    <h3 class="details-title">Order Information</h3>
                    <div id="orderDetails">
                        <!-- Order details will be loaded here -->
                    </div>
                </div>

                <div class="order-items">
                    <h3 class="details-title">Order Items</h3>
                    <div id="orderItems">
                        <!-- Order items will be loaded here -->
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="shop.php" class="btn-primary">
                        <i class="fas fa-store me-2"></i>
                        Continue Shopping
                    </a>
                    <a href="orders.php" class="btn-outline-primary">
                        <i class="fas fa-box me-2"></i>
                        View All Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const orderId = <?php echo json_encode($order_id); ?>;

        // Load order details
        function loadOrderDetails() {
            $.ajax({
                url: 'api/get_order_details.php',
                type: 'GET',
                data: { order_id: orderId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayOrderDetails(response.data);
                        displayOrderItems(response.data.items);
                    }
                }
            });
        }

        // Display order details
        function displayOrderDetails(order) {
            const container = document.getElementById('orderDetails');
            container.innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Order Number:</span>
                    <span class="detail-value">${order.order_number}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Order Date:</span>
                    <span class="detail-value">${new Date(order.created_at).toLocaleDateString()}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">${order.status}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value">$${order.total_amount}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Shipping Address:</span>
                    <span class="detail-value">${order.shipping_address}</span>
                </div>
            `;
        }

        // Display order items
        function displayOrderItems(items) {
            const container = document.getElementById('orderItems');
            let html = '';

            items.forEach(item => {
                const totalPrice = (item.price * item.quantity).toFixed(2);
                html += `
                    <div class="order-item">
                        <div class="item-image">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="item-details">
                            <div class="item-name">${item.name}</div>
                            <div class="item-price">$${item.price} x ${item.quantity}</div>
                        </div>
                        <div class="item-total">$${totalPrice}</div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        // User profile dropdown
        $('.user-profile').on('click', function(e) {
            e.stopPropagation();
            $('.dropdown-menu').toggleClass('show');
        });
        
        $(document).on('click', function() {
            $('.dropdown-menu').removeClass('show');
        });

        // Load order details on page load
        $(document).ready(function() {
            loadOrderDetails();
        });
    </script>
</body>
</html>
