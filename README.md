# Furni House - Bootstrap PHP AJAX Login & Register System

A modern, responsive furniture management dashboard with user authentication, built using Bootstrap 5, PHP, AJAX, and MySQL. Inspired by the Furni House design with a beautiful gradient interface.

## Features

- 🔐 **Secure Authentication System**
  - User registration with validation
  - Login with username/email
  - Password hashing with bcrypt
  - CSRF protection
  - Session management

- 🎨 **Modern UI/UX**
  - Bootstrap 5 responsive design
  - Beautiful gradient backgrounds
  - Glassmorphism effects
  - Interactive charts with Chart.js
  - Font Awesome icons

- 📊 **Dashboard Features**
  - Real-time analytics
  - Income and spending tracking
  - Product management
  - Popular products display
  - Interactive data visualization

- 🔒 **Security Features**
  - SQL injection prevention with prepared statements
  - XSS protection
  - CSRF token validation
  - Input sanitization
  - Secure password hashing

## File Structure

```
furni-house-system/
├── config/
│   └── database.php          # Database configuration
├── includes/
│   └── functions.php         # Utility functions
├── auth/
│   ├── login.php            # Login handler
│   ├── register.php         # Registration handler
│   └── logout.php           # Logout handler
├── api/
│   ├── get_analytics.php    # Analytics API
│   └── get_products.php     # Products API
├── index.php                # Login/Register page
├── dashboard.php            # Main dashboard
├── database_setup.sql       # Database schema
└── README.md               # This file
```

## Setup Instructions for AwardSpace

### 1. Database Setup

1. **Access phpMyAdmin**
   - Log in to your AwardSpace control panel
   - Navigate to "Databases" → "phpMyAdmin"
   - Create a new database or use an existing one

2. **Import Database Schema**
   - Open phpMyAdmin
   - Select your database
   - Go to "Import" tab
   - Upload the `database_setup.sql` file
   - Click "Go" to execute the SQL

3. **Database Configuration**
   - Edit `config/database.php`
   - Replace the following values:
     ```php
     private $db_name = 'your_database_name';
     private $username = 'your_awardspace_mysql_username';
     private $password = 'your_awardspace_mysql_password';
     ```

### 2. File Upload

1. **Upload Files**
   - Use FTP or AwardSpace File Manager
   - Upload all files to your web directory (usually `public_html/`)
   - Maintain the folder structure

2. **File Permissions**
   - Ensure PHP files have 644 permissions
   - Directories should have 755 permissions

### 3. Configuration

1. **Database Connection**
   - Update database credentials in `config/database.php`
   - Test the connection

2. **Default Login Credentials**
   - **Admin User:**
     - Username: `admin`
     - Email: `admin@furnihouse.com`
     - Password: `password`
   - **Regular User:**
     - Username: `user1`
     - Email: `user1@furnihouse.com`
     - Password: `password`

### 4. Testing

1. **Access the Application**
   - Navigate to `yourdomain.com/index.php`
   - You should see the login/register page

2. **Test Registration**
   - Create a new account
   - Verify email validation
   - Test password confirmation

3. **Test Login**
   - Use the default credentials or your new account
   - Verify dashboard access

4. **Test Dashboard**
   - Check if analytics load
   - Verify product data displays
   - Test responsive design

## Technical Details

### Database Tables

- **users**: User authentication and profile data
- **products**: Product inventory and details
- **analytics**: Income and spending tracking

### Security Features

- **Password Hashing**: Uses PHP's `password_hash()` with bcrypt
- **SQL Injection Prevention**: Prepared statements with PDO
- **XSS Protection**: Input sanitization and output escaping
- **CSRF Protection**: Token-based request validation
- **Session Security**: Secure session handling

### AJAX Endpoints

- `auth/login.php`: User authentication
- `auth/register.php`: User registration
- `api/get_analytics.php`: Dashboard analytics data
- `api/get_products.php`: Product listing data

## Customization

### Styling
- Modify CSS in the `<style>` sections of each file
- Update color schemes by changing gradient values
- Customize Bootstrap classes for layout changes

### Functionality
- Add new API endpoints in the `api/` directory
- Extend dashboard features in `dashboard.php`
- Modify validation rules in `includes/functions.php`

### Database
- Add new tables as needed
- Modify existing table structures
- Update sample data in `database_setup.sql`

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify database credentials
   - Check if MySQL is enabled in AwardSpace
   - Ensure database exists

2. **500 Internal Server Error**
   - Check file permissions
   - Verify PHP version compatibility
   - Check error logs in AwardSpace

3. **AJAX Not Working**
   - Verify jQuery is loading
   - Check browser console for errors
   - Ensure API endpoints are accessible

4. **Session Issues**
   - Check PHP session configuration
   - Verify session directory permissions
   - Clear browser cookies

### AwardSpace Specific

1. **PHP Version**
   - AwardSpace supports PHP 7.4+
   - Ensure compatibility with your hosting plan

2. **MySQL Access**
   - Use AwardSpace MySQL credentials
   - Database name format: `username_database`

3. **File Upload**
   - Use AwardSpace File Manager for easy upload
   - Maintain folder structure exactly

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Dependencies

- **Bootstrap 5.3.0**: UI framework
- **jQuery 3.6.0**: JavaScript library
- **Chart.js**: Data visualization
- **Font Awesome 6.0.0**: Icons
- **PHP 7.4+**: Server-side language
- **MySQL 5.7+**: Database

## License

This project is open source and available under the MIT License.

## Support

For issues specific to AwardSpace hosting:
1. Check AwardSpace documentation
2. Contact AwardSpace support
3. Verify hosting plan limitations

For application issues:
1. Check browser console for errors
2. Verify file permissions
3. Test database connectivity
4. Review PHP error logs

## Updates

- **v1.0.0**: Initial release with basic authentication and dashboard
- Future updates will include:
  - Product management CRUD operations
  - Advanced analytics
  - User roles and permissions
  - Email notifications
  - Mobile app integration
