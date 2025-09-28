<?php
require_once 'includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('index.php');
}

$user_name = $_SESSION['full_name'] ?? $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furni House - My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .orders-container {
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
        
        .orders-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .orders-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .orders-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .filter-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .filter-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            background: white;
            transition: all 0.3s ease;
        }
        
        .filter-select:focus {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
        }
        
        .order-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .order-info {
            flex: 1;
        }
        
        .order-number {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }
        
        .order-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .status-processing {
            background: rgba(0, 123, 255, 0.1);
            color: #007bff;
        }
        
        .status-shipped {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .status-delivered {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .status-cancelled {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .order-items {
            margin-bottom: 1rem;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
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
        
        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .order-total {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }
        
        .order-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            border: none;
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(255, 107, 107, 0.3);
            color: white;
        }
        
        .btn-outline-primary {
            border: 2px solid #ff6b6b;
            color: #ff6b6b;
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: #ff6b6b;
            color: white;
        }
        
        .empty-orders {
            text-align: center;
            padding: 4rem 2rem;
            color: #6c757d;
        }
        
        .empty-orders i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #ff6b6b;
        }
        
        .empty-orders h3 {
            margin-bottom: 1rem;
            color: #333;
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
            
            .filter-controls {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <div class="orders-container">
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
                        <h1>My Orders</h1>
                        <p>Track your order history and status</p>
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

            <!-- Orders Content -->
            <div class="orders-content">
                <div class="orders-header">
                    <h2 class="orders-title">Order History</h2>
                    <div class="filter-controls">
                        <select class="filter-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <select class="filter-select" id="sortFilter">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="amount-high">Highest Amount</option>
                            <option value="amount-low">Lowest Amount</option>
                        </select>
                    </div>
                </div>

                <div id="ordersList">
                    <!-- Orders will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let orders = [];

        // Load orders
        function loadOrders() {
            $.ajax({
                url: 'api/get_orders.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        orders = response.data.orders;
                        displayOrders(orders);
                    }
                }
            });
        }

        // Display orders
        function displayOrders(ordersToShow) {
            const container = document.getElementById('ordersList');
            
            if (ordersToShow.length === 0) {
                container.innerHTML = `
                    <div class="empty-orders">
                        <i class="fas fa-box-open"></i>
                        <h3>No orders found</h3>
                        <p>You haven't placed any orders yet.</p>
                        <a href="shop.php" class="btn btn-primary">Start Shopping</a>
                    </div>
                `;
                return;
            }

            let html = '';
            ordersToShow.forEach(order => {
                const statusClass = `status-${order.status}`;
                const orderDate = new Date(order.created_at).toLocaleDateString();
                
                html += `
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <div class="order-number">${order.order_number}</div>
                                <div class="order-date">Ordered on ${orderDate}</div>
                            </div>
                            <div class="order-status ${statusClass}">${order.status}</div>
                        </div>
                        
                        <div class="order-items">
                            ${order.items.map(item => `
                                <div class="order-item">
                                    <div class="item-image">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="item-details">
                                        <div class="item-name">${item.name}</div>
                                        <div class="item-price">$${item.price} x ${item.quantity}</div>
                                    </div>
                                    <div class="item-total">$${(item.price * item.quantity).toFixed(2)}</div>
                                </div>
                            `).join('')}
                        </div>
                        
                        <div class="order-footer">
                            <div class="order-total">Total: $${order.total_amount}</div>
                            <div class="order-actions">
                                <a href="order_details.php?id=${order.id}" class="btn-sm btn-outline-primary">View Details</a>
                                ${order.status === 'pending' ? `<a href="#" class="btn-sm btn-primary" onclick="cancelOrder(${order.id})">Cancel Order</a>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        // Filter orders
        function filterOrders() {
            const statusFilter = document.getElementById('statusFilter').value;
            const sortFilter = document.getElementById('sortFilter').value;
            
            let filtered = orders.filter(order => {
                return !statusFilter || order.status === statusFilter;
            });

            // Sort orders
            filtered.sort((a, b) => {
                switch(sortFilter) {
                    case 'oldest':
                        return new Date(a.created_at) - new Date(b.created_at);
                    case 'amount-high':
                        return b.total_amount - a.total_amount;
                    case 'amount-low':
                        return a.total_amount - b.total_amount;
                    default: // newest
                        return new Date(b.created_at) - new Date(a.created_at);
                }
            });

            displayOrders(filtered);
        }

        // Cancel order
        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order?')) {
                $.ajax({
                    url: 'api/cancel_order.php',
                    type: 'POST',
                    data: { order_id: orderId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showToast('Order cancelled successfully', 'success');
                            loadOrders();
                        } else {
                            showToast(response.message, 'error');
                        }
                    }
                });
            }
        }

        // Show toast notification
        function showToast(message, type) {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `
                <div class="toast-body d-flex align-items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle text-success' : 'exclamation-circle text-danger'} me-2"></i>
                    ${message}
                </div>
            `;
            
            // Add to page
            document.body.appendChild(toast);
            
            // Show toast
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            }, 100);

            // Remove toast after 3 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        // Event listeners
        document.getElementById('statusFilter').addEventListener('change', filterOrders);
        document.getElementById('sortFilter').addEventListener('change', filterOrders);

        // User profile dropdown
        $('.user-profile').on('click', function(e) {
            e.stopPropagation();
            $('.dropdown-menu').toggleClass('show');
        });
        
        $(document).on('click', function() {
            $('.dropdown-menu').removeClass('show');
        });

        // Load orders on page load
        $(document).ready(function() {
            loadOrders();
        });
    </script>
</body>
</html>
