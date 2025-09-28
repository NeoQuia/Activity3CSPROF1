<?php
require_once 'includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    if (is_admin()) {
        redirect('dashboard.php');
    } else {
        redirect('shop.php');
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        .auth-header {
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .auth-logo {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .auth-subtitle {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        .form-container {
            padding: 2rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }
        .nav-tabs {
            border: none;
            margin-bottom: 2rem;
        }
        .nav-tabs .nav-link {
            border: none;
            border-radius: 10px;
            margin-right: 10px;
            padding: 12px 25px;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            color: white;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
        }
        .input-group .form-control {
            border-left: none;
        }
        .password-toggle {
            cursor: pointer;
            color: #6c757d;
        }
        .loading {
            display: none;
        }
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-home"></i> 
                </div>
                <div class="auth-subtitle">Welcome to your management dashboard</div>
            </div>
            
            <div class="form-container">
                <ul class="nav nav-tabs" id="authTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="authTabsContent">
                    <!-- Login Form -->
                    <div class="tab-pane fade show active" id="login" role="tabpanel">
                        <form id="loginForm">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="alert alert-danger" id="loginAlert" style="display: none;"></div>
                            <div class="alert alert-success" id="loginSuccess" style="display: none;"></div>

                            <div class="mb-3">
                                <label for="loginUsername" class="form-label">Username or Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" id="loginUsername" name="username" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="loginPassword" name="password" required>
                                    <span class="input-group-text password-toggle" onclick="togglePassword('loginPassword')">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <span class="loading">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Logging in...
                                </span>
                                <span class="normal">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </span>
                            </button>
                        </form>
                    </div>

                    <!-- Register Form -->
                    <div class="tab-pane fade" id="register" role="tabpanel">
                        <form id="registerForm">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="alert alert-danger" id="registerAlert" style="display: none;"></div>
                            <div class="alert alert-success" id="registerSuccess" style="display: none;"></div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="registerFullName" class="form-label">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" class="form-control" id="registerFullName" name="full_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="registerUsername" class="form-label">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-at"></i>
                                        </span>
                                        <input type="text" class="form-control" id="registerUsername" name="username" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="registerEmail" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="registerEmail" name="email" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="registerPassword" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" id="registerPassword" name="password" required>
                                        <span class="input-group-text password-toggle" onclick="togglePassword('registerPassword')">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="registerConfirmPassword" class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" id="registerConfirmPassword" name="confirm_password" required>
                                        <span class="input-group-text password-toggle" onclick="togglePassword('registerConfirmPassword')">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <span class="loading">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Creating account...
                                </span>
                                <span class="normal">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function showAlert(alertId, message, type) {
            const alert = document.getElementById(alertId);
            alert.className = `alert alert-${type}`;
            alert.textContent = message;
            alert.style.display = 'block';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }

        function setLoading(formId, loading) {
            const form = document.getElementById(formId);
            const button = form.querySelector('button[type="submit"]');
            const loadingSpan = button.querySelector('.loading');
            const normalSpan = button.querySelector('.normal');
            
            if (loading) {
                loadingSpan.style.display = 'inline';
                normalSpan.style.display = 'none';
                button.disabled = true;
            } else {
                loadingSpan.style.display = 'none';
                normalSpan.style.display = 'inline';
                button.disabled = false;
            }
        }

        // Login Form Handler
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            
            setLoading('loginForm', true);
            
            $.ajax({
                url: 'auth/login.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    setLoading('loginForm', false);
                    
                    if (response.success) {
                        showAlert('loginSuccess', response.message, 'success');
                        setTimeout(() => {
                            window.location.href = 'dashboard.php';
                        }, 1500);
                    } else {
                        showAlert('loginAlert', response.message, 'danger');
                    }
                },
                error: function() {
                    setLoading('loginForm', false);
                    showAlert('loginAlert', 'An error occurred. Please try again.', 'danger');
                }
            });
        });

        // Register Form Handler
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();
            
            const password = $('#registerPassword').val();
            const confirmPassword = $('#registerConfirmPassword').val();
            
            if (password !== confirmPassword) {
                showAlert('registerAlert', 'Passwords do not match.', 'danger');
                return;
            }
            
            setLoading('registerForm', true);
            
            $.ajax({
                url: 'auth/register.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    setLoading('registerForm', false);
                    
                    if (response.success) {
                        showAlert('registerSuccess', response.message, 'success');
                        $('#registerForm')[0].reset();
                        // Switch to login tab
                        $('#login-tab').tab('show');
                    } else {
                        showAlert('registerAlert', response.message, 'danger');
                    }
                },
                error: function() {
                    setLoading('registerForm', false);
                    showAlert('registerAlert', 'An error occurred. Please try again.', 'danger');
                }
            });
        });
    </script>
</body>
</html>
