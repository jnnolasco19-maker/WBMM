# WBMM RBAC Security Documentation

## Overview
This document explains the Role-Based Access Control (RBAC) system implemented for the Web Market Management System (WBMM), including authentication security, access control enforcement, and endpoint protection.

## 1. RBAC Enforcement

### Role Hierarchy
The system implements a four-tier role hierarchy:

- **Admin (Level 100)**: Full system access
  - Manage users, roles, products, orders, reports
  - All permissions granted
  
- **Manager (Level 75)**: Operational management
  - Manage products, inventory, and orders
  - View reports
  - Cannot manage users/roles
  
- **Staff (Level 50)**: Product and inventory management
  - Add and update products
  - Manage inventory
  - Cannot access user management or reports
  
- **Cashier (Level 25)**: Sales operations
  - View products
  - Create orders/sales
  - Limited dashboard access

### Database Structure

#### Roles Table
```sql
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    level INT NOT NULL DEFAULT 0,
    created_at DATETIME,
    updated_at DATETIME
);
```

#### Users Table (Updated)
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL,  -- Legacy field for backward compatibility
    role_id INT UNSIGNED,  -- New foreign key to roles table
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL ON UPDATE CASCADE
);
```

#### Permissions Table (Optional Advanced Feature)
```sql
CREATE TABLE permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    permission_name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at DATETIME,
    updated_at DATETIME
);
```

#### Role Permissions Table
```sql
CREATE TABLE role_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    created_at DATETIME,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY (role_id, permission_id)
);
```

### Access Control Implementation

#### Role Filter (`app/Filters/RoleFilter.php`)
The RoleFilter enforces role-based access control on protected routes:

```php
public function before(RequestInterface $request, $arguments = null)
{
    // Check authentication first
    if (! session()->get('is_logged_in')) {
        return redirect()->to('/login')->with('error', 'Authentication required.');
    }

    // If no role arguments, just check login
    if (empty($arguments)) {
        return;
    }

    $userRole = session()->get('user_role');
    
    // Check if user has any of the required roles
    $hasAccess = false;
    
    foreach ($arguments as $requiredRole) {
        // Direct role match
        if (strtolower($userRole) === strtolower($requiredRole)) {
            $hasAccess = true;
            break;
        }
        
        // Admin has access to everything
        if (strtolower($userRole) === 'admin') {
            $hasAccess = true;
            break;
        }
    }

    if (! $hasAccess) {
        return redirect()->to('/dashboard')
            ->with('error', 'Access Denied. You do not have permission to access this page.');
    }
}
```

#### Permission Filter (`app/Filters/PermissionFilter.php`)
The PermissionFilter provides granular permission-based access control:

```php
public function before(RequestInterface $request, $arguments = null)
{
    // Check authentication
    if (! session()->get('is_logged_in')) {
        return redirect()->to('/login')->with('error', 'Authentication required.');
    }

    // Admin bypass
    if (strtolower(session()->get('user_role')) === 'admin') {
        return;
    }

    // Check specific permissions
    $userId = session()->get('user_id');
    $userModel = new UserModel();
    $user = $userModel->findWithRole($userId);
    
    $permissionModel = new PermissionModel();
    $hasPermission = false;
    
    foreach ($arguments as $requiredPermission) {
        if ($permissionModel->roleHasPermission($user['role_id'], $requiredPermission)) {
            $hasPermission = true;
            break;
        }
    }

    if (! $hasPermission) {
        return redirect()->to('/dashboard')
            ->with('error', 'Access Denied. You do not have the required permissions.');
    }
}
```

## 2. Authentication Security

### Password Hashing
All passwords are hashed using PHP's `password_hash()` with PASSWORD_BCRYPT algorithm:

```php
$password = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
```

### Password Verification
Passwords are verified using `password_verify()` during login:

```php
if (! $user || ! password_verify($password, $user['password'])) {
    // Invalid credentials
}
```

### Session Management
- **Session Regeneration**: Sessions are regenerated on successful login to prevent session fixation attacks
- **Secure Session Storage**: Session data includes user ID, name, role, role ID, and role level
- **Session Timeout**: Configured via PHP session settings

```php
session()->regenerate(true);
session()->set([
    'user_id'          => $user['id'],
    'user_name'        => $user['name'],
    'user_role'        => $roleName,
    'user_role_id'     => $user['role_id'] ?? null,
    'user_role_level'  => $roleLevel,
    'is_logged_in'     => true,
]);
```

### Rate Limiting
The login system implements IP-based rate limiting to prevent brute force attacks:

```php
$cache = \Config\Services::cache();
$ip = $this->request->getIPAddress();
$keyAtt = 'login_attempts_' . md5($ip);
$keyBlk = 'login_blocked_' . md5($ip);

// Block after 5 failed attempts for 5 minutes
$attempts = (int) $cache->get($keyAtt) + 1;
$cache->save($keyAtt, $attempts, 300);

if ($attempts >= 5) {
    $cache->save($keyBlk, time() + 300, 300);
    return redirect()->back()
        ->with('error', 'Too many failed attempts. You are blocked for 5 minutes.');
}
```

### CSRF Protection
All forms are protected by CSRF tokens via the global CSRF filter:

```php
'globals' => [
    'before' => [
        'auth' => ['except' => ['login', 'login/*', 'forgot-password', 'forgot-password/*', 'register', 'register/*', 'reset-password', 'reset-password/*']],
        'csrf',
    ],
],
```

## 3. Endpoint Protection

### Route-Based Protection
Routes are protected using filter aliases in the routes configuration:

```php
// Dashboard - authentication required
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);

// User management - admin only
$routes->get('users', 'UserController::index', ['filter' => 'auth:role:admin']);

// Vendors - admin and manager
$routes->get('vendors', 'VendorController::index', ['filter' => 'auth:role:admin,manager']);

// Records - admin, manager, and cashier
$routes->get('records', 'RecordController::index', ['filter' => 'auth:role:admin,manager,cashier']);
```

### Filter Configuration
Filters are registered in `app/Config/Filters.php`:

```php
public array $aliases = [
    'auth'       => AuthFilter::class,
    'role'       => RoleFilter::class,
    'permission' => PermissionFilter::class,
    'csrf'       => CSRF::class,
    // ... other filters
];
```

### Access Control Matrix

| Route/Module | Admin | Manager | Staff | Cashier |
|--------------|-------|---------|-------|---------|
| Dashboard | ✓ | ✓ | ✓ | ✓ |
| User Management | ✓ | ✗ | ✗ | ✗ |
| Vendor Management | ✓ | ✓ | ✗ | ✗ |
| Stall Management | ✓ | ✓ | ✗ | ✗ |
| Record View | ✓ | ✓ | ✗ | ✓ |
| Record Create | ✓ | ✓ | ✗ | ✓ |
| Record Edit | ✓ | ✓ | ✗ | ✗ |
| Record Delete | ✓ | ✗ | ✗ | ✗ |
| Profile | ✓ | ✓ | ✓ | ✓ |

## 4. SQL Injection Prevention

### Prepared Statements
All database queries use CodeIgniter's Query Builder which automatically uses prepared statements:

```php
// Safe - uses prepared statements
$user = $this->where('email', $email)->first();

// Safe - uses prepared statements
$this->db->table('users')->insert($data);
```

### Model-Based Access
Database access is restricted through models with defined allowed fields:

```php
protected $allowedFields = ['name', 'email', 'password', 'role', 'role_id'];
```

## 5. Security Best Practices Implemented

### Input Validation
All user inputs are validated using CodeIgniter's validation library:

```php
if (! $this->validate([
    'email'    => 'required|valid_email',
    'password' => 'required|min_length[8]|max_length[72]',
])) {
    return redirect()->back()
        ->withInput()
        ->with('errors', $this->validator->getErrors());
}
```

### Email Enumeration Prevention
Password reset and registration always show the same message regardless of whether the email exists:

```php
// Always show the same message to prevent email enumeration
$confirmation = 'If that email is registered, a reset link has been sent.';
```

### Secure Logout
Logout destroys the entire session:

```php
public function logout(): object
{
    session()->destroy();
    return redirect()->to('/login')->with('message', 'You have been logged out successfully.');
}
```

### Database Connection Security
Database credentials are stored in `.env` file (not in code):

```env
database.default.hostname = localhost
database.default.database = wbmm_db
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

## 6. Installation and Setup

### Step 1: Run Migrations
```bash
php spark migrate
```

### Step 2: Run RBAC Seeder
```bash
php spark db:seed RBACSeeder
```

### Step 3: Run Auth Seeder
```bash
php spark db:seed AuthSeeder
```

### Step 4: Test Login Credentials
After running seeders, you can test with these credentials:

- **Admin**: admin@wbmm.com / Admin@1234
- **Manager**: manager@wbmm.com / Manager@1234
- **Staff**: staff@wbmm.com / Staff@1234
- **Cashier**: cashier@wbmm.com / Cashier@1234

## 7. Testing Access Control

### Test Admin Access
1. Login as admin@wbmm.com
2. Try accessing all routes - should have full access

### Test Manager Access
1. Login as manager@wbmm.com
2. Can access vendors, stalls, records
3. Cannot access user management (/users)
4. Should see "Access Denied" message

### Test Staff Access
1. Login as staff@wbmm.com
2. Cannot access vendors, stalls, or user management
3. Should see "Access Denied" message

### Test Cashier Access
1. Login as cashier@wbmm.com
2. Can access records (view and create)
3. Cannot access vendors, stalls, or user management
4. Should see "Access Denied" message

## 8. Maintenance and Updates

### Adding New Roles
1. Add role to `RBACSeeder.php`
2. Run seeder: `php spark db:seed RBACSeeder`
3. Update route filters as needed

### Adding New Permissions
1. Add permission to `RBACSeeder.php`
2. Assign to appropriate roles in seeder
3. Run seeder: `php spark db:seed RBACSeeder`
4. Use permission filter on routes: `['filter' => 'auth:permission:new_permission']`

### Modifying Role Access
1. Update route filters in `app/Config/Routes.php`
2. Update role permissions in `RBACSeeder.php`
3. Run seeder: `php spark db:seed RBACSeeder`

## 9. Security Recommendations

### Production Deployment
1. Change default admin passwords immediately
2. Enable HTTPS (forcehttps filter)
3. Set strong session expiration times
4. Implement logging for access attempts
5. Regular security audits
6. Keep CodeIgniter and dependencies updated

### Additional Security Measures
1. Implement two-factor authentication
2. Add CAPTCHA to login forms
3. Implement account lockout policies
4. Add security headers (CSP, X-Frame-Options, etc.)
5. Regular database backups
6. Monitor for suspicious activity

## 10. Troubleshooting

### Migration Issues
If migrations fail, check:
- Database credentials in `.env`
- MySQL server is running
- Database exists and user has permissions

### Access Denied Errors
If users get unexpected access denied:
- Check session data: `print_r(session()->get());`
- Verify role_id is set in users table
- Check filter configuration in routes
- Verify role names match exactly (case-sensitive)

### Permission Issues
If permission-based access fails:
- Ensure RBACSeeder has been run
- Check role_permissions table has data
- Verify permission names match exactly
- Check user has role_id assigned

---

**Document Version**: 1.0  
**Last Updated**: 2026-05-28  
**System**: WBMM (Web Market Management System)  
**Framework**: CodeIgniter 4
