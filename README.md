# Web-Based Market Management System (WBMM)
### General Santos City Public Market Administration Portal

A premium, server-side rendered **CodeIgniter 4** web application designed to manage public market operations, automate daily rental (Arkalaba) collections, audit cashier logs, generate thermal receipt e-tickets, and stream real-time treasury collection reports.

---

## 🛠️ Technology Stack
* **Backend:** CodeIgniter 4 (PHP 8.x)
* **Database:** MySQL 8.x
* **Server environment:** Apache via XAMPP
* **Styling (CSS):** Bootstrap 5 via CDN + Premium Custom HSL Glassmorphic CSS
* **Visual Graphics:** Chart.js via CDN (monthly collection aggregations)
* **Auth:** Native CI4 Session-based authentication with bcrypt password hashes

---

## 🚀 Setup & Installation Instructions

Follow these steps to deploy the portal locally using XAMPP:

### 1. File Setup
1. Download or clone this project repository.
2. Place the project folder (`WBMM`) inside your local XAMPP root directory: `C:\xampp\htdocs\WBMM`.

### 2. Database Import
1. Ensure Apache and MySQL are running inside your **XAMPP Control Panel**.
2. Open **phpMyAdmin** in your browser (`http://localhost/phpmyadmin`).
3. Click the **Import** tab.
4. Browse and select the [schema.sql](file:///c:/xampp/htdocs/WBMM/schema.sql) file located in the root folder of this project.
5. Click **Go** to create the `wbmm_db` database, structure the tables, and seed the default accounts.

### 3. Environment Configuration
1. Open the [.env](file:///c:/xampp/htdocs/WBMM/.env) file in your text editor.
2. Verify the base URL matches your localhost execution path:
   ```env
   app.baseURL = 'http://localhost/WBMM/public/'
   ```
3. Verify the database configurations:
   ```env
   database.default.hostname = localhost
   database.default.database = wbmm_db
   database.default.username = root
   database.default.password = 
   ```

### 4. Run the Portal
* Access the login gateway in your web browser: **`http://localhost/WBMM/public/`**

---

## 🔑 Default Credentials

Use these pre-seeded accounts to verify and test the RBAC access configurations:

| Role | Email Address | Password | Privileges Level |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `admin@wbmm.com` | `Admin@1234` | **Full CRUD** (Full access, system audits, user management) |
| **Staff Personnel** | `staff@wbmm.com` | `Staff@1234` | **Read + Create only** (Restricted from edits, deletions, and logs) |

---

## 📊 Role & Permissions Matrix

The portal implements strict controller-level and UI-level **Role-Based Access Control (RBAC)**:

| System Path / Feature | Mapped Route | Administrator (`admin`) | Staff (`staff`) |
| :--- | :--- | :---: | :---: |
| **Dashboard Metrics** | `/dashboard` | View All stats + Chart | View basic stats + Chart |
| **Register Vendor** | `/vendors/create` | **Allowed** | **Allowed** |
| **Modify Vendor** | `/vendors/edit/{id}` | **Allowed** | *Blocked (403)* |
| **Remove Vendor** | `/vendors/delete/{id}` | **Allowed** | *Blocked (403)* |
| **Collect Arkalaba** | `/payments/create` | **Allowed** | **Allowed** |
| **Print Lease Receipts** | `/payments/receipt/{id}` | **Allowed** | **Allowed** |
| **Export Collections CSV** | `/records/export` | **Allowed** | **Allowed** |
| **Audit Logs Console** | `/records/audit-logs` | **Allowed** | *Blocked (403)* |
| **Staff User CRUD** | `/users/*` | **Allowed** | *Blocked (403)* |

---

## 🛡️ Security Features
* **SQL Injection Protection:** Engineered strictly using CodeIgniter 4's parameterized **Query Builder** (no raw SQL concatenations).
* **CSRF Token Guards:** Enabled globally for all post operations.
* **Authentication Middleware:** The global `AuthFilter` interceptor secures all dashboard routes and rejects unauthenticated guests.
* **Audit Logs:** Every update, registration, deletion, and auth action logs a record in the `audit_logs` table detailing who performed the action, which table was affected, and the timestamp.
