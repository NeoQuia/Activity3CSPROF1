<?php
require_once 'includes/functions.php';

// Check if user is logged in and is admin
if (!is_logged_in()) {
    redirect('index.php');
}

if (!is_admin()) {
    redirect('shop.php');
}

$user_name = $_SESSION['full_name'] ?? $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furni House - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .dashboard-container {
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
        
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .card-header {
            background: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 1.5rem 2rem;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .metric-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .metric-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .metric-change {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .metric-change.up {
            color: #28a745;
        }
        
        .metric-change.down {
            color: #ffc107;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-available {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .status-sold-out {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .table {
            margin: 0;
        }
        
        .table th {
            border: none;
            font-weight: 600;
            color: #6c757d;
            padding: 1rem;
        }
        
        .table td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
        }
        
        .product-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.5);
            margin-bottom: 1rem;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .product-info h6 {
            margin: 0;
            font-weight: 600;
        }
        
        .product-info p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .rating {
            color: #ffc107;
        }
        
        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .btn-icon:hover {
            background: #ff6b6b;
            color: white;
        }
        
        .notification-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            border-radius: 10px;
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
        
        .notification-content h6 {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #333;
        }
        
        .notification-content p {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
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
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-home"></i>
                </div>
            </div>
            
            <nav class="nav-menu">
                <div class="nav-item">
                    <a href="#" class="nav-link active" data-section="overview">
                        <i class="fas fa-tachometer-alt"></i>
                        Overview
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" data-section="chat">
                        <i class="fas fa-comments"></i>
                        Chat
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" data-section="analytics">
                        <i class="fas fa-chart-line"></i>
                        Analytics
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" data-section="sale">
                        <i class="fas fa-tags"></i>
                        Sale
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" data-section="review">
                        <i class="fas fa-star"></i>
                        Review
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" data-section="product">
                        <i class="fas fa-box"></i>
                        Product
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" data-section="orders">
                        <i class="fas fa-shopping-cart"></i>
                        Orders
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" data-section="notifications">
                        <i class="fas fa-bell"></i>
                        Notifications
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1>Dashboard</h1>
                        <p>Welcome, manage and analyze your products</p>
                    </div>
                    <div class="col-md-6">
                        <div class="header-actions justify-content-end">
                            <button class="btn-icon" title="Search">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn-icon" title="Notifications">
                                <i class="fas fa-bell"></i>
                            </button>
                            <div class="user-profile dropdown">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                                </div>
                                <span>Hi, <?php echo $user_name; ?></span>
                                <i class="fas fa-chevron-down"></i>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="auth/logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metrics Row -->
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="metric-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Income</h6>
                                <div class="metric-value" id="incomeValue">$0.00</div>
                                <div class="metric-change up" id="incomeChange">
                                    <i class="fas fa-arrow-up"></i>
                                    Up to 24%
                                </div>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="metric-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Spending</h6>
                                <div class="metric-value" id="spendingValue">$0.00</div>
                                <div class="metric-change down" id="spendingChange">
                                    <i class="fas fa-arrow-down"></i>
                                    Down to 45%
                                </div>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="metric-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Total Orders</h6>
                                <div class="metric-value" id="totalOrders">0</div>
                                <div class="metric-change up">
                                    <i class="fas fa-arrow-up"></i>
                                    New orders
                                </div>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="metric-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-2">Total Sales</h6>
                                <div class="metric-value" id="totalSales">$0.00</div>
                                <div class="metric-change up">
                                    <i class="fas fa-arrow-up"></i>
                                    Revenue
                                </div>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Chart -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Analytics</h5>
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-sm" style="width: auto;">
                                    <option>Short by</option>
                                </select>
                                <select class="form-select form-select-sm" style="width: auto;">
                                    <option>Product</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="analyticsChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Popular</h5>
                        </div>
                        <div class="card-body" id="popularProducts">
                            <!-- Popular products will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card" id="productsSection">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Products</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" onclick="showAddProductModal()">
                            <i class="fas fa-plus me-1"></i>Add Product
                        </button>
                        <select class="form-select form-select-sm" style="width: auto;">
                            <option>Sort by</option>
                        </select>
                        <select class="form-select form-select-sm" style="width: auto;">
                            <option>All</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="productsTable">
                            <thead>
                                <tr>
                                    <th>ID Product</th>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Products will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card" id="ordersSection" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Orders</h5>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="orderStatusFilter">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="ordersTable">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Orders will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notifications Section -->
            <div class="card" id="notificationsSection" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notifications</h5>
                    <button class="btn btn-outline-primary btn-sm" onclick="markAllAsRead()">
                        <i class="fas fa-check me-1"></i>Mark All as Read
                    </button>
                </div>
                <div class="card-body">
                    <div id="notificationsList">
                        <!-- Notifications will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Load dashboard data
        function loadDashboardData() {
            // Load analytics
            $.ajax({
                url: 'api/get_analytics.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateAnalytics(response.data);
                    }
                }
            });

            // Load products
            $.ajax({
                url: 'api/get_products.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateProducts(response.data);
                    }
                }
            });
        }

        function updateAnalytics(data) {
            $('#incomeValue').text('$' + data.current_month.income);
            $('#spendingValue').text('$' + data.current_month.spending);
            $('#totalOrders').text(data.sales_stats.total_orders);
            $('#totalSales').text('$' + data.sales_stats.total_sales);
            
            const incomeChange = data.changes.income;
            const spendingChange = data.changes.spending;
            
            $('#incomeChange').html(`<i class="fas fa-arrow-${incomeChange >= 0 ? 'up' : 'down'}"></i> ${incomeChange >= 0 ? 'Up' : 'Down'} to ${Math.abs(incomeChange)}%`);
            $('#spendingChange').html(`<i class="fas fa-arrow-${spendingChange >= 0 ? 'up' : 'down'}"></i> ${spendingChange >= 0 ? 'Up' : 'Down'} to ${Math.abs(spendingChange)}%`);
            
            // Update chart
            updateChart(data.weekly_data);
            
            // Update best-selling products
            updateBestSellingProducts(data.best_selling);
        }

        function updateProducts(data) {
            // Update products table
            let tableHtml = '';
            data.products.forEach(product => {
                const statusClass = product.status === 'Available' ? 'status-available' : 'status-sold-out';
                tableHtml += `
                    <tr>
                        <td>${product.product_id}</td>
                        <td>${product.name}</td>
                        <td>${product.quantity}</td>
                        <td>$${product.price}</td>
                        <td><span class="status-badge ${statusClass}">${product.status}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="editProduct(${product.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            $('#productsTable tbody').html(tableHtml);
        }

        function updateBestSellingProducts(products) {
            let popularHtml = '';
            products.forEach(product => {
                popularHtml += `
                    <div class="product-item">
                        <div class="product-image">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="product-info flex-grow-1">
                            <h6>${product.name}</h6>
                            <div class="rating">
                                <span class="ms-1">Sold: ${product.total_sold}</span>
                            </div>
                            <p>Revenue: $${product.total_revenue}</p>
                        </div>
                    </div>
                `;
            });
            $('#popularProducts').html(popularHtml);
        }

        function updateChart(weeklyData) {
            const ctx = document.getElementById('analyticsChart').getContext('2d');
            
            if (window.analyticsChart) {
                window.analyticsChart.destroy();
            }
            
            const labels = weeklyData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('en-US', { weekday: 'short' });
            });
            
            const incomeData = weeklyData.map(item => parseFloat(item.income));
            const spendingData = weeklyData.map(item => parseFloat(item.spending));
            
            window.analyticsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Income',
                        data: incomeData,
                        borderColor: '#ff6b6b',
                        backgroundColor: 'rgba(255, 107, 107, 0.1)',
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Spending',
                        data: spendingData,
                        borderColor: '#ffa500',
                        backgroundColor: 'rgba(255, 165, 0, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Navigation
        $('.nav-link').on('click', function(e) {
            e.preventDefault();
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            
            const section = $(this).data('section');
            showSection(section);
        });

        // Show specific section
        function showSection(section) {
            // Hide all sections
            $('#productsSection, #ordersSection, #notificationsSection').hide();
            
            switch(section) {
                case 'product':
                    $('#productsSection').show();
                    loadProducts();
                    break;
                case 'orders':
                    $('#ordersSection').show();
                    loadOrders();
                    break;
                case 'notifications':
                    $('#notificationsSection').show();
                    loadNotifications();
                    break;
                default:
                    $('#productsSection').show();
                    loadProducts();
            }
        }

        // User profile dropdown functionality
        $('.user-profile').on('click', function(e) {
            e.stopPropagation();
            $('.dropdown-menu').toggleClass('show');
        });
        
        // Close dropdown when clicking outside
        $(document).on('click', function() {
            $('.dropdown-menu').removeClass('show');
        });

        // Load orders
        function loadOrders() {
            $.ajax({
                url: 'api/get_admin_orders.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayOrders(response.data.orders);
                    }
                }
            });
        }

        // Display orders
        function displayOrders(orders) {
            let tableHtml = '';
            orders.forEach(order => {
                const statusClass = `status-${order.status}`;
                const orderDate = new Date(order.created_at).toLocaleDateString();
                
                tableHtml += `
                    <tr>
                        <td>${order.order_number}</td>
                        <td>${order.customer_name}</td>
                        <td>$${order.total_amount}</td>
                        <td><span class="status-badge ${statusClass}">${order.status}</span></td>
                        <td>${orderDate}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="viewOrder(${order.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="updateOrderStatus(${order.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            $('#ordersTable tbody').html(tableHtml);
        }

        // Load notifications
        function loadNotifications() {
            $.ajax({
                url: 'api/get_notifications.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayNotifications(response.data.notifications);
                    }
                }
            });
        }

        // Display notifications
        function displayNotifications(notifications) {
            let html = '';
            notifications.forEach(notification => {
                const isRead = notification.is_read ? 'read' : 'unread';
                const timeAgo = getTimeAgo(notification.created_at);
                
                html += `
                    <div class="notification-item ${isRead}" onclick="markAsRead(${notification.id})">
                        <div class="notification-content">
                            <h6>${notification.title}</h6>
                            <p>${notification.message}</p>
                            <small class="text-muted">${timeAgo}</small>
                        </div>
                        ${!notification.is_read ? '<div class="notification-badge"></div>' : ''}
                    </div>
                `;
            });
            $('#notificationsList').html(html);
        }

        // Update order status
        function updateOrderStatus(orderId) {
            const newStatus = prompt('Enter new status (pending, processing, shipped, delivered, cancelled):');
            if (newStatus && ['pending', 'processing', 'shipped', 'delivered', 'cancelled'].includes(newStatus)) {
                $.ajax({
                    url: 'api/update_order_status.php',
                    type: 'POST',
                    data: { order_id: orderId, status: newStatus },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showToast('Order status updated successfully', 'success');
                            loadOrders();
                        } else {
                            showToast(response.message, 'error');
                        }
                    }
                });
            }
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

        // Load data on page load
        $(document).ready(function() {
            loadDashboardData();
            showSection('product'); // Show products by default
            
            // Refresh data every 30 seconds
            setInterval(loadDashboardData, 30000);
        });
    </script>
</body>
</html>
