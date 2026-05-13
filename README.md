# Web-Based Market Management System (WBMM)

Group: 5  
Members:
- Johnnoel B. Nolasco
- Mohairudin G. Ali
- Cathrina T. Fado
- Adrianne M. Lozada
- Charles Keyan Mark T. Tudal

## Description
The Web-Based Market Management System (WBMM) is a web-based application designed to efficiently manage public market vendors, stalls, and records. It allows market administrators and staff to:
- Authenticate securely via a login system
- View a role-based dashboard with system analytics
- Add, update, and remove vendors
- Assign and track market stalls
- Maintain daily records and activity logs

## Technologies Used
- Backend Framework: CodeIgniter 4 (PHP)
- Database: MySQL (via XAMPP)
- Frontend: Bootstrap 5 (CDN)
- IDE: Visual Studio Code
- Version Control: Git / GitHub

## Modules

### 1. Login Module (Johnnoel B. Nolasco)
- Session-based authentication
- Credential validation against the `users` table using bcrypt
- Login error handling and rate limiting (5 attempts = 10 min block)
- Password recovery via email token
- Route protection via `AuthFilter`

### 2. Dashboard Module (Mohairudin G. Ali)
- Displays welcome message with user name and role
- Role-based stat cards (admin sees all 5, staff sees 2)
- Quick action shortcuts based on role
- Responsive Bootstrap 5 layout with hamburger menu on mobile

### 3. Records Module (Cathrina T. Fado)
- Full CRUD for Vendors, Stalls, and Records
- Search and filter on all list pages
- Role-based access: admin = full CRUD, staff = read + create records only
- Data validation and CSRF protection on all forms

### 4. Integration & Testing (Charles Keyan Mark T. Tudal)
- Merged all modules into a single codebase
- Resolved base URL issues for XAMPP subfolder deployment
- Verified seamless user flow: login → dashboard → records

### 5. Version Control & Documentation (Charles Keyan Mark T. Tudal)
- Maintained GitHub repository
- Documented setup instructions and module descriptions

## Setup Instructions

1. Clone the repository:
   ```
   git clone https://github.com/your-team/WBMM.git
   ```
2. Move project to `C:/xampp/htdocs/WBMM`
3. Copy `env` to `.env` and configure:
   ```
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost/WBMM/public/'
   database.default.hostname = localhost
   database.default.database = wbmm_db
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   ```
4. Start Apache and MySQL in XAMPP
5. Create database `wbmm_db` in phpMyAdmin
6. Run migrations:
   ```
   php spark migrate
   ```
7. Seed the default admin user:
   ```
   php spark db:seed AuthSeeder
   ```
8. Open: http://localhost/WBMM/public/login

## Default Login Credentials
- Email: `admin@wbmm.com`
- Password: `Admin@1234`

- Email: `staff@wbmm.com`
- Password: `Staff@1234`
## System Workflow
```
[Login] → [Dashboard] → [Vendors / Stalls / Records]
              ↓
         Role Check
         Admin: Full CRUD + all stats
         Staff: Read-only + create records
```

## Known Issues and Resolutions
| Issue | Resolution |
|-------|-----------|
| Redirects going to `localhost/login` instead of `localhost/WBMM/public/login` | Set `app.baseURL` in `.env` and updated all form actions and links to use `base_url()` |
| `migrations` table appearing in database | Normal CI4 behavior — tracks which migrations have run |
