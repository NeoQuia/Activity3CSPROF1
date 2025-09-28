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
    <title>Furni House - Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .notifications-container {
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
        
        .notifications-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .notifications-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .notifications-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .notification-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn-mark-all {
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-mark-all:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(255, 107, 107, 0.3);
        }
        
        .notification-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .notification-item.unread {
            background: rgba(255, 107, 107, 0.05);
            border-left: 4px solid #ff6b6b;
        }
        
        .notification-item.read {
            background: rgba(255, 255, 255, 0.5);
            border-left: 4px solid #e9ecef;
        }
        
        .notification-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .notification-icon.order-status {
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
        }
        
        .notification-icon.new-order {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .notification-icon.general {
            background: rgba(0, 123, 255, 0.1);
            color: #007bff;
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .notification-message {
            color: #6c757d;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        
        .notification-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .notification-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ff6b6b;
        }
        
        .empty-notifications {
            text-align: center;
            padding: 4rem 2rem;
            color: #6c757d;
        }
        
        .empty-notifications i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #ff6b6b;
        }
        
        .empty-notifications h3 {
            margin-bottom: 1rem;
            color: #333;
        }
        
        .filter-controls {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
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
            }
        }
    </style>
</head>
<body>
    <div class="notifications-container">
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
                    <a href="orders.php" class="nav-link">
                        <i class="fas fa-box"></i>
                        My Orders
                    </a>
                </div>
                <div class="nav-item">
                    <a href="notifications.php" class="nav-link active">
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
                        <h1>Notifications</h1>
                        <p>Stay updated with your orders and account</p>
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

            <!-- Notifications Content -->
            <div class="notifications-content">
                <div class="notifications-header">
                    <h2 class="notifications-title">Your Notifications</h2>
                    <div class="notification-actions">
                        <button class="btn-mark-all" onclick="markAllAsRead()">
                            <i class="fas fa-check me-1"></i>
                            Mark All as Read
                        </button>
                    </div>
                </div>

                <div class="filter-controls">
                    <select class="filter-select" id="typeFilter">
                        <option value="">All Types</option>
                        <option value="order_status">Order Status</option>
                        <option value="new_order">New Order</option>
                        <option value="general">General</option>
                    </select>
                    <select class="filter-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="unread">Unread</option>
                        <option value="read">Read</option>
                    </select>
                </div>

                <div id="notificationsList">
                    <!-- Notifications will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let notifications = [];

        // Load notifications
        function loadNotifications() {
            $.ajax({
                url: 'api/get_notifications.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        notifications = response.data.notifications;
                        displayNotifications(notifications);
                    }
                }
            });
        }

        // Display notifications
        function displayNotifications(notificationsToShow) {
            const container = document.getElementById('notificationsList');
            
            if (notificationsToShow.length === 0) {
                container.innerHTML = `
                    <div class="empty-notifications">
                        <i class="fas fa-bell-slash"></i>
                        <h3>No notifications</h3>
                        <p>You don't have any notifications yet.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            notificationsToShow.forEach(notification => {
                const isRead = notification.is_read ? 'read' : 'unread';
                const timeAgo = getTimeAgo(notification.created_at);
                const iconClass = `notification-icon ${notification.type.replace('_', '-')}`;
                const icon = getNotificationIcon(notification.type);
                
                html += `
                    <div class="notification-item ${isRead}" onclick="markAsRead(${notification.id})">
                        <div class="${iconClass}">
                            <i class="fas fa-${icon}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">${notification.title}</div>
                            <div class="notification-message">${notification.message}</div>
                            <div class="notification-time">${timeAgo}</div>
                        </div>
                        ${!notification.is_read ? '<div class="notification-badge"></div>' : ''}
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        // Get notification icon
        function getNotificationIcon(type) {
            switch(type) {
                case 'order_status':
                    return 'shipping-fast';
                case 'new_order':
                    return 'shopping-cart';
                default:
                    return 'bell';
            }
        }

        // Filter notifications
        function filterNotifications() {
            const typeFilter = document.getElementById('typeFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            
            let filtered = notifications.filter(notification => {
                const matchesType = !typeFilter || notification.type === typeFilter;
                const matchesStatus = !statusFilter || 
                    (statusFilter === 'unread' && !notification.is_read) ||
                    (statusFilter === 'read' && notification.is_read);
                return matchesType && matchesStatus;
            });

            displayNotifications(filtered);
        }

        // Mark notification as read
        function markAsRead(notificationId) {
            $.ajax({
                url: 'api/mark_notification_read.php',
                type: 'POST',
                data: { notification_id: notificationId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        loadNotifications();
                    }
                }
            });
        }

        // Mark all notifications as read
        function markAllAsRead() {
            $.ajax({
                url: 'api/mark_all_notifications_read.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast('All notifications marked as read', 'success');
                        loadNotifications();
                    }
                }
            });
        }

        // Get time ago
        function getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' minutes ago';
            if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago';
            return Math.floor(diffInSeconds / 86400) + ' days ago';
        }

        // Show toast notification
        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `
                <div class="toast-body d-flex align-items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle text-success' : 'exclamation-circle text-danger'} me-2"></i>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            }, 100);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        // Event listeners
        document.getElementById('typeFilter').addEventListener('change', filterNotifications);
        document.getElementById('statusFilter').addEventListener('change', filterNotifications);

        // User profile dropdown
        $('.user-profile').on('click', function(e) {
            e.stopPropagation();
            $('.dropdown-menu').toggleClass('show');
        });
        
        $(document).on('click', function() {
            $('.dropdown-menu').removeClass('show');
        });

        // Load notifications on page load
        $(document).ready(function() {
            loadNotifications();
        });
    </script>
</body>
</html>
